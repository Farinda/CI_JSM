<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pemeriksaan_import extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('pemeriksaan_import_model');
    $this->load->library('excel');
  }

  function index()
  {
    $this->load->view('pemeriksaan_import');
  }

  function fetch()
  {
    $data = $this->pemeriksaan_import_model->select();
    $output = '
    <h3 align="center">Total Data - '.$data->num_rows().'</h3>
    <table class="table table-striped table-bordered">
    <tr>
      <th>id_ruang</th>
      <th>nama</th>
      <th>pic</th>
      <th>tanggalcek</th>
    </tr>
    ';
    foreach($data->result() as $row)
    {
      $output .= '
      <tr>
        <td>'.$row->id.'</td>
        <td>'.$row->id_ruang.'</td>
        <td>'.$row->pic.'</td>
        <td>'.$row->tanggalcek.'</td>
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
          $id_ruang = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
          $pic = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
          $tanggalcek = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
          $data[] = array(
            'id'  => $id,
            'id_ruang'   => $id_ruang,
            'pic' => $pic,
            'tanggalcek' => $tanggalcek,
          );
        }
      }
        $this->pemeriksaan_import_model->insert($data);
        echo 'Data Imported successfully';
    } 
  }
}

?>