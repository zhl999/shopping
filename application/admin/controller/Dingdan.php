<?php
namespace app\admin\controller;
use gmars\rbac\Rbac;
use think\facade\Session;
use Db;
use Request;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Dingdan extends Common
{
	public function list()
	{
		return $this->fetch();
	}
    public function daochu()
    {
    	//require_once __DIR__ . '/../../src/Bootstrap.php';

    	$helper = new Sample();
    	if ($helper->isCli()) {
    	    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

    	    return;
    	}
    	// Create new Spreadsheet object
    	$spreadsheet = new Spreadsheet();

    	// Set document properties
    	$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
    	    ->setLastModifiedBy('Maarten Balliauw')
    	    ->setTitle('Office 2007 XLSX Test Document')
    	    ->setSubject('Office 2007 XLSX Test Document')
    	    ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
    	    ->setKeywords('office 2007 openxml php')
    	    ->setCategory('Test result file');

    	// Add some data
    	$arr=Db::query("select * from dingdan");
    	// var_dump($arr);die;
    	$spreadsheet->setActiveSheetIndex(0)
    	    ->setCellValue('A1', 'id')
    	    ->setCellValue('B1', 'not_pay')
    	    ->setCellValue('C1', 'payed')
    	    ->setCellValue('D1', 'not_shipped!')
		    ->setCellValue('E1', 'shipped!');
    	foreach ($arr as $key => $value) {
    		$i=$key+2;
    		$spreadsheet->setActiveSheetIndex(0)
    		    ->setCellValue('A'.$i, $value['id'])
    		    ->setCellValue('B'.$i, $value['not_pay'])
    		    ->setCellValue('C'.$i, $value['payed'])
    		    ->setCellValue('D'.$i, $value['not_shipped'])
    		    ->setCellValue('E'.$i, $value['not_shipped']);
    	}
    	

    	// Miscellaneous glyphs, UTF-8
    	$spreadsheet->setActiveSheetIndex(0)
    	    ->setCellValue('A4', 'Miscellaneous glyphs')
    	    ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

    	// Rename worksheet
    	$spreadsheet->getActiveSheet()->setTitle('Simple');

    	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
    	$spreadsheet->setActiveSheetIndex(0);

    	// Redirect output to a client’s web browser (Xlsx)
    	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    	header('Content-Disposition: attachment;filename="01simple.xlsx"');
    	header('Cache-Control: max-age=0');
    	// If you're serving to IE 9, then the following may be needed
    	header('Cache-Control: max-age=1');

    	// If you're serving to IE over SSL, then the following may be needed
    	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    	header('Pragma: public'); // HTTP/1.0

    	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    	$writer->save('php://output');
    	exit;
    }
    public function aa()
    {
    	$helper= new Sample();
    	//require __DIR__ . '/../Header.php';
    	$myfile = request()->file('myfile');
    	$info = $myfile->move( './excel/');
    	if ($info) {
    		 $files= $info->getSaveName();
    		 $inputFileName="./excel/".$files;
    	    //$inputFileName = __DIR__ . '/sampleData/a1.xlsx';
	    	$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory to identify the format');
	    	$spreadsheet = IOFactory::load($inputFileName);
	    	$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
	    	//var_dump($sheetData);die;
	    	// $sheetData=array_shift($sheetData);
	    	foreach ($sheetData as $key => $value) {
	    		$data = ['not_pay' => $value['B'], 'payed' =>$value['C'],'not_shipped'=>$value['D'],'shipped'=>$value['E']];
	    		Db::name('dingdan')->insert($data);
	    	}
	    	$json=['code'=>'0','status'=>'ok','data'=>'上传成功'];
	    	echo json_encode($json);die;
    	}
    }
}