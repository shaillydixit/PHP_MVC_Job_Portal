<?php 

namespace App\Controllers;
use Framework\Database;
use Framework\Validation;

class ListingController {

    protected $db;
    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    public function index()
    {
        $listings = $this->db->query('SELECT * FROM listings')->fetchAll();

        loadView('listings/index', ['listings' => $listings]);
        
    }

    public function create()
    {
        loadView('listings/create');
    }

    public function store()
    {
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        $newListingData['user_id'] = 1;

        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = ['title', 'description', 'email', 'city', 'state','salary'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
        } else {
            $fields = [];
            $values = [];

            foreach ($newListingData as $field => $value) {
                if ($value === '') {
                    $newListingData[$field] = null; // Convert empty string to null
                }
                $fields[] = $field;
                $values[] = ':' . $field;
            }

            $fields = implode(', ', $fields);
            $values = implode(', ', $values);

            // Use double quotes for string interpolation
            $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";

            $this->db->query($query, $newListingData);
            redirect('/listings');
        }
    }


    public function show($params)
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id,
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    public function destroy($params)
    {
        $id = $params['id'];
        $params = [
            'id' => $id
        ];
        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();
        if(!$listing){
            ErrorController::notFound('Listing Not Found');
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', $params);
        redirect('/listings');
    }
}