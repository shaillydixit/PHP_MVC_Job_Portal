<?php 

namespace App\Controllers;

use Framework\Database;

class HomeController {

    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        // Assign the new Database instance to the $db property of the class
        $this->db = new Database($config);
    }

    public function index()
    {
        // Now $this->db is properly initialized and can be used
        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC LIMIT 6')->fetchAll();

        loadView('home', ['listings' => $listings]);
    }
} 
