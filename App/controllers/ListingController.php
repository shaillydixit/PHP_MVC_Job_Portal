<?php 

namespace App\Controllers;
use Framework\Database;
use Framework\Session;
use Framework\Validation;
use Framework\Authorization;
class ListingController {

    protected $db;
    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    public function index()
    {
        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC')->fetchAll();

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

        $newListingData['user_id'] = Session::get('user')['id'];

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
            Session::setFlashMessage('success_message', 'Listing Created Successfully');

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

        if(!Authorization::isQwner($listing->user_id))
        {
            Session::setFlashMessage('error_message', 'You are not authorized to delete this listing');
           return redirect('/listings/' . $listing->id);
        }
        $this->db->query('DELETE FROM listings WHERE id = :id', $params);

        Session::setFlashMessage('success_message', 'Listing Deleted Successfully');

        redirect('/listings');
    }


    public function edit($params)
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
        if(!Authorization::isQwner($listing->user_id))
        {
            Session::setFlashMessage('error_message', 'You are not authorized to update this listing');
           return redirect('/listings/' . $listing->id);
        }
        loadView('listings/edit', [
            'listing' => $listing
        ]);
    }

    
    public function update($params)
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
        if(!Authorization::isQwner($listing->user_id))
        {
            Session::setFlashMessage('error_message', 'You are not authorized to update this listing');
           return redirect('/listings/' . $listing->id);
        }
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];
   
        $updateValues = [];
        $updateValues = array_intersect_key($_POST, array_flip($allowedFields));
        $updateValues = array_map('sanitize', $updateValues);

        $requiredFields = ['title', 'description', 'email', 'city', 'state','salary'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if(!empty($errors))
        {
            loadView('listings/edit', [
                'listing' => $listing,
                'errors' => $errors
            ]);
            exit;
        }else{
            $updateFields = [];
            foreach(array_keys($updateValues) as $field){
                $updateFields[] = "{$field} = :{$field}";
            }
            $updateFields = implode(', ', $updateFields);
            $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";
            $updateValues['id'] = $id;
            $this->db->query($updateQuery, $updateValues);
            Session::setFlashMessage('success_message', 'Listing Updated Successfully');
            redirect('/listings/'. $id);
        }
    }

    public function search()
    {
        $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';

        $query = "SELECT * FROM listings WHERE (title LIKE :keywords OR description LIKE :keywords OR tags LIKE :keywords OR company LIKE :keywords) AND (city LIKE :location OR state LIKE :location)";

        $params = [
            'keywords' => "%{$keywords}%",
            'location' => "%{$location}%",
        ];
        $listings = $this->db->query($query, $params)->fetchAll();

        loadView('/listings/index', [
            'listings' => $listings,
            'keywords' => $keywords,
            'location' => $location,

        ]);
    }

}