<?php
class ShopProducts
{
    protected $conn;
    protected $input;

    public function __construct($conn, $data)
    {
        $this->conn = $conn;
        $this->input = $data;
    }

    public function getProducts()
    {
        $db = new Sql($this->conn);

        $sql = "select 
            p.product_id as id, 
            p.name as name,
            p.description as beschreibung,
            p.image as bild, 
            p.tax as mehrwertsteuer,
            p.price as preis,
            concat('https://www.google.com/search?q=',p.name) as link,
            p.stock as lagerstand,
            c.category_id  as kategorieId,
            c.name  as Kategorie
        from products p 
            left join product_categories pc on p.product_id =pc.product_id
            left join categories c  on c.category_id =pc.category_id  
        where 
            p.lockstate=0 
            and pc.lockstate=0
            and c.category_id=? ";

        $data = $db->executeSQL($sql, [1], 'i', true);
        return $db->convertDbResultToJson($data);
    }

    
}
