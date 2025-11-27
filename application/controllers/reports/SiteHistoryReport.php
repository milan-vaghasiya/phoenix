<?php
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
			
			if(!empty($data['file_type']) && $data['file_type'] == 'PDF' && empty($projectData)){
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
				
				//Generate PDF
				if(!empty($data['file_type']) && $data['file_type'] == 'PDF')
				{
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
					ob_clean();	
				}
			}
			$this->printJson(['status' => 1, 'tbody' => $tbody]);
        }
    }
}
?>