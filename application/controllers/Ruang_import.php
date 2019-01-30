<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ruang_import extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('ruang_import_model');
    $this->load->library('excel');
  }

  function index()
  {
    $this->load->view('ruang_import');
  }

  function fetch()
  {
    $data = $this->ruang_import_model->select();
    $output = '
    <h3 align="center">Total Data - '.$data->num_rows().'</h3>
    <table class="table table-striped table-bordered">
    <tr>
      <th>id</th>
      <th>nama</th>
    </tr>
    ';
    foreach($data->result() as $row)
    {
      $output .= '
      <tr>
        <td>'.$row->id.'</td>
        <td>'.$row->nama.'</td>
      </tr>
      ';
    }
      $output .= '</table>';
      echo $output;
     }

   function import()
   {
    if(isset($_FILES["file"]["name"]))
    {
      $path = $_FILES["file"]["tmp_name"];
      $object = PHPExcel_IOFactory::load($path);
      foreach($object->getWorksheetIterator() as $worksheet)
      {
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        for($row=2; $row<=$highestRow; $row++)
        {
          $id = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
          $nama = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
          $data[] = array(
            'id'  => $id,
            'nama'   => $nama,
          );
        }
      }
        $this->ruang_import_model->insert($data);
        echo 'Data Imported successfully';
    } 
  }
}

?>