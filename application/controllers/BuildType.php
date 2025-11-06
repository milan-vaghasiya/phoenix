<?php
class BuildType extends MY_Controller{
    private $index = "build_type/index";
    private $form = "build_type/form";

    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Build Type";
        $this->data['headData']->controller = "buildType";
        $this->data['headData']->pageUrl = "buildType";
    }

    public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->buildType->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getBuildTypeData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addBuildType(){
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['build_type']))
            $errorMessage['build_type'] = "Build Type is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->buildType->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->buildType->getBuildTypeDetail($data);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->buildType->delete($id));
        endif;
    }
}
?>