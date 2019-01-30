<?php
class Pemeriksaan_import_model extends CI_Model
{
    function select()
    {
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get('tb_pemeriksaan');
        return $query;
    }

    function insert($data)
    {
        $this->db->insert_batch('tb_pemeriksaan', $data);
    }
}