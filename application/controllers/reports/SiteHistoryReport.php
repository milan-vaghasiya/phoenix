<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

ini_set('max_execution_time', 500);
ini_set('memory_limit', '1024M');
class SiteHistoryReport extends MY_Controller
{
    private $site_history = "report/site_history_report/site_history";  

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
        $this->data['headData']->pageTitle = "SITE HISTORY REPORT";
		$this->data['headData']->controller = "reports/siteHistoryReport";
	}
	
    public function index(){
        $this->data['headData']->pageTitle = "SITE HISTORY";
		$this->data['pageHeader'] = 'SITE HISTORY REPORT';
		$this->data['projectList'] = $this->project->getProjectList();
        $this->load->view($this->site_history,$this->data);
    }

	public function getSiteHistory($jsonData=''){
        if(!empty($jsonData)){
			$data = (Array) decodeURL($jsonData);
		} else{
			$data = $this->input->post();
		}
        $errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)){
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
		} else{
			$data['order_by'] = "ASC";
            $projectData = $this->projectHistory->getProjectHistory($data);
            $tbody = ""; $i = 1; 
			
			if(!empty($data['file_type']) && ($data['file_type'] == 'PDF' || $data['file_type'] == 'excel') && empty($projectData)){
				redirect(base_url('reports/siteHistoryReport')); 
			}		

            if(!empty($projectData)){
				$chunks = array_chunk($projectData, 500);

				foreach($chunks as $chunk){		
					foreach ($chunk as $row) { 	
						$media_file = (!empty($row->media_file) ? '<a href="'.$row->media_file.'" target="_blank" download=""><img src="'.$row->media_file.'" class="img-thumbnails" height="50" width="50"></a>' : 'N/A');

						$tbody .= '<tr>
							<td class="text-center">'.$i++.'</td>
							<td class="text-left" style="padding-left:10px; padding-right:10px;">'.$row->project_name.'</td>
							<td class="text-left" style="padding-left:10px; padding-right:10px;">'.$row->user_name.'</td>
							<td class="text-center" style="padding-left:10px; padding-right:10px;">'.formatDate($row->created_at, 'd-m-Y h:i:s A').'</td>
							<td class="text-left" style="padding-left:10px; padding-right:10px;">'.$row->message.'</td>
							<td class="text-center" style="padding:5px;">'.$media_file.'</td>';
						$tbody.='</tr>';
					}
				}
				
				$reportTitle = 'Site History Report';
				$thead = '<tr style="background:#dddddd;">
						<th colspan="6">'.$reportTitle.'</th>
					</tr>							
					<tr style="background:#dddddd;">
						<th style="min-width:50px;">#</th>
						<th style="min-width:200px;">Project Name</th>
						<th style="min-width:120px;">Created By</th>
						<th style="min-width:100px;">Created Date</th>
						<th style="min-width:150px;">Message</th>
						<th style="min-width:100px;">Media</th>
					</tr>'; 

				//Generate PDF
				if(!empty($data['file_type']) && $data['file_type'] == 'PDF')
				{										
					$mpdf = new \Mpdf\Mpdf();
					$pdfFileName = 'SiteHistoryReport_'.str_replace(["/","-"],"_",date('d-m-Y')).'.pdf';          
					$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
					$mpdf->WriteHTML($stylesheet, 1);
					$mpdf->SetDisplayMode('fullpage');
					$mpdf->SetTitle($reportTitle);
					$mpdf->AddPage('L','','','','',5,5,15,10,3,3,'','','','','','','','','','A4-L');

					$mpdf->WriteHTML('<table border="1" width="100%" style="border-collapse:collapse;"><thead>'.$thead.'</thead><tbody>');
					$mpdf->WriteHTML($tbody, \Mpdf\HTMLParserMode::HTML_BODY);
					$mpdf->WriteHTML('</tbody></table>');

					$mpdf->Output($pdfFileName, 'I');
					ob_clean();	exit;
				}
				else if (!empty($data['file_type']) && $data['file_type'] == 'excel') {
					$spreadsheet = new Spreadsheet();
					$sheet = $spreadsheet->getActiveSheet();
					$sheet->setTitle($reportTitle);

					$sheet->setCellValue("A1", "#");
					$sheet->setCellValue("B1", "Project Name");
					$sheet->setCellValue("C1", "Created By");
					$sheet->setCellValue("D1", "Created Date");
					$sheet->setCellValue("E1", "Message");
					$sheet->setCellValue("F1", "Media");

					$sheet->getStyle("A1:F1")->getFont()->setBold(true);

					$sheet->getColumnDimension('A')->setAutoSize(true);   
					$sheet->getColumnDimension('B')->setAutoSize(true);  
					$sheet->getColumnDimension('C')->setAutoSize(true); 
					$sheet->getColumnDimension('D')->setAutoSize(true); 
					$sheet->getColumnDimension('E')->setAutoSize(true); 
					$sheet->getColumnDimension('F')->setWidth(20);

					$row = 2;
					foreach ($projectData as $key => $item) {
						$sheet->setCellValue("A{$row}", $key + 1);
						$sheet->setCellValue("B{$row}", $item->project_name);
						$sheet->setCellValue("C{$row}", $item->user_name);
						$sheet->setCellValue("D{$row}", formatDate($item->created_at, 'd-m-Y h:i:s A'));
						$sheet->setCellValue("E{$row}", $item->message);

						if (!empty($item->media_file)) {
							$path = FCPATH . ltrim(parse_url($item->media_file, PHP_URL_PATH), "/");

							if (file_exists($path)) {
								$targetHeight = 100;
								$targetWidth  = 100;

								$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
								$drawing->setName('Image');
								$drawing->setDescription('Media Image');
								$drawing->setPath($path);

								$drawing->setHeight($targetHeight);
								$drawing->setWidth($targetWidth);
								$drawing->setCoordinates("F{$row}");
								$drawing->setOffsetX(5);
								$drawing->setOffsetY(5);
								$drawing->setWorksheet($sheet);

								$padding = 10;
								$sheet->getRowDimension($row)->setRowHeight($targetHeight + $padding);
							}
						}
						$row++;
					}

					$writer = new Xlsx($spreadsheet);
					$filename = 'SiteHistoryReport_'.str_replace(["/","-"],"_",date('d-m-Y')).'.xlsx';

					ob_clean();
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					$writer->save("php://output");
					exit;
				}
			}
			$this->printJson(['status' => 1, 'tbody' => $tbody]);
        }
    }
}
?>