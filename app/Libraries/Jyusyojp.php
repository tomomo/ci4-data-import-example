<?php
/**
 * Jyusyojp
 *
 * HACK: Modelで実装予定であったが、既知の不具合があるため一線を画すためにあえてライブラリにした。
 * https://github.com/codeigniter4/CodeIgniter4/issues/4099
 *
 * 住所.jp(http://jusyo.jp/)のCSVを扱うライブラリ
 */

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Jyusyojp class
 *
 * @package CodeIgniter
 */
class Jyusyojp {

	/**
	 * テーブル名
	 *
	 * @var string テーブル名
	 */
	private $table = 'ext_jusyojp_zenkoku';

	/**
	 * アップロードディレクトリ
	 *
	 * @var string
	 */
	private $uploadDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'jusyojp';

	/**
	 * CSVアップロード処理
	 *
	 * @param UploadedFile $file ファイル
	 *
	 * @return mixed
	 */
	public function upload(UploadedFile $file)
	{
		$path = $this->doOptimization($file);
		if (! $path)
		{
			throw new \Exception('パスがありません。');
		}
		return $this->doImport($path);
	}

	/**
	 * CSVアップロード処理
	 *
	 * @param UploadedFile $file ファイル
	 *
	 * @return mixed
	 */
	public function upload2(UploadedFile $file)
	{
		$path = $this->doOptimization($file);
		if (! $path)
		{
			throw new \Exception('パスがありません。');
		}
		return $this->doImport2($path);
	}

	/**
	 * アップロードファイルの最適化
	 *
	 * @param UploadedFile $file ファイル
	 *
	 * @return string
	 */
	private function doOptimization(UploadedFile $file)
	{
		$fileName = $file->getRandomName();

		$filePath = $this->uploadDir . DIRECTORY_SEPARATOR . $fileName;
		$file->move($this->uploadDir, $fileName);

		if ($file->getClientExtension() === 'zip')
		{
			$zip = new \ZipArchive();

			$fileDir = rtrim($filePath, '.zip');
			$zip->open($filePath);
			$zip->extractTo($fileDir);
			$zip->close();
			unset($zip);
			unlink($filePath);

			$filePaths = array_map(function ($file) {
				// NOTE: Original data is in Shift JIS format. Conversion to UTF-8.
				$data = mb_convert_encoding(@file_get_contents($file), 'UTF-8', 'SJIS-Win');
				file_put_contents($file, $data);
				unset($data);
				return $file;
			}, glob($fileDir . '/*.csv'));

			$filePath = $filePaths[0] ?? null;
		}

		log_message('debug', 'Jusyojp::filePath: ' . $filePath);
		return $filePath;
	}

	/**
	 * 中間テーブルへインポート
	 *
	 * @param string $filePath ファイル
	 *
	 * @return mixed
	 */
	public function doImport(string $filePath)
	{
		set_time_limit(120);
		ini_set('memory_limit', '480M');

		$db     = \Config\Database::connect();
		$fields = $db->getFieldData($this->table);

		$objFileCsv = new \CodeIgniter\Files\File($filePath);
		$splFileCsv = $objFileCsv->openFile('r');
		$splFileCsv->setFlags(
			\SplFileObject::READ_CSV |
			\SplFileObject::READ_AHEAD |
			\SplFileObject::SKIP_EMPTY |
			\SplFileObject::DROP_NEW_LINE
		);

		$splFileCsv->seek(1);
		$rows = [];
		while (! $splFileCsv->eof())
		{
			$currentRow = $splFileCsv->current();

			$row = [];
			foreach ($fields as $key => $field)
			{
				$row[$field->name] = $currentRow[$key] ?? null;
			}
			$rows[] = $row;
			$splFileCsv->next();
		}
		unset($splFileCsv);

		#
		# TODO: Bug: Using insertBatch with a large amount of data will result in an error.
		# https://github.com/codeigniter4/CodeIgniter4/issues/4099
		#

		$db->table($this->table)->truncate();
		// TODO: result Error! Allowed memory size of
		// $db->table($this->table)->insertBatch($rows,null,80);

		// TODO: Alternative proposal
		$db->table($this->table)->truncate();
		foreach (array_chunk($rows, 80) as $chunk)
		{
			$db->table($this->table)->insertBatch($chunk);
		}

		unset($rows);

		return $db->table($this->table)->countAll();
	}

	/**
	 * 中間テーブルへインポート
	 *
	 * @param string $filePath ファイル
	 *
	 * @return mixed
	 */
	public function doImport2(string $filePath)
	{
		set_time_limit(120);
		ini_set('memory_limit', '480M');

		$db     = \Config\Database::connect();
		$fields = $db->getFieldData($this->table);

		$objFileCsv = new \CodeIgniter\Files\File($filePath);
		$splFileCsv = $objFileCsv->openFile('r');
		$splFileCsv->setFlags(
			\SplFileObject::READ_CSV |
			\SplFileObject::READ_AHEAD |
			\SplFileObject::SKIP_EMPTY |
			\SplFileObject::DROP_NEW_LINE
		);

		$splFileCsv->seek(1);
		$rows = [];
		while (! $splFileCsv->eof())
		{
			$currentRow = $splFileCsv->current();

			$row = [];
			foreach ($fields as $key => $field)
			{
				$row[$field->name] = $currentRow[$key] ?? null;
			}
			$rows[] = $row;
			$splFileCsv->next();
		}
		unset($splFileCsv);

		#
		# TODO: Bug: Using insertBatch with a large amount of data will result in an error.
		# https://github.com/codeigniter4/CodeIgniter4/issues/4099
		#

		$db->table($this->table)->truncate();
		// TODO: result Error! Allowed memory size of
		// $db->table($this->table)->insertBatch($rows);
		// TODO: result Error! Allowed memory size of
		$db->table($this->table)->insertBatch($rows,null,80);

		unset($rows);

		return $db->table($this->table)->countAll();
	}

}
