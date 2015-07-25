<?php namespace App\Paka\Transformers;

use App\Paka\Transformers\ExpensesTransformer;
use CouchDB;

class CategoriesTransformer extends Transformer {

    protected $views;
    protected $categoriesMap;
    protected $expensesTransformer;

    public function __construct(){
        $this->expensesTransformer = new ExpensesTransformer();
        $this->views['by_user'] = '_design/categories/_view/by_user';
        $this->views['by_date'] = '_design/categories/_view/by_date';
    }

    /**
     * Returns all generic categories
     *
     * @return array with categories
     */
    public function all()
    {
        $rawCategories = CouchDB::executeAuth('get', $this->buildUrl('by_user'));
        $categories = $this->transformCollection($rawCategories->rows);

        return $categories;
    }

    /**
     * Creates a new category for the current user
     *
     * @param array $categoryData
     * @return array
     */
    public function insert($categoryData)
    {
        $user = CouchDB::getUser();
        $doc = new \stdClass();
        $doc->name = $categoryData['name'];
        $doc->color = $categoryData['color'];
        $doc->type = 'category';
        $doc->user_id = $user->name;

        $response = CouchDB::executeAuth('post', $this->database, [
            'json' => $doc
        ]);

        return $response;
    }

    /**
     * Find the category with the given id
     *
     * @param $id
     * @return array
     */
    public function find($id)
    {
        $category = CouchDB::executeAuth('get',  $this->buildUrl('by_user', [
            'key' => [$id]
        ]));

        if(!$category->rows){
            abort(404, "Category Not found");
        }

        return $this->transform($category->rows[0]);
    }

    /**
     * Updates the category with the given id
     *
     * @param string $id
     * @param array $categoryData with updated data
     * @return mixed
     */
    public function update($id, $categoryData)
    {

        $category = $this->find($id);

        $category->_rev = $categoryData['_rev'];
        $category->name = $categoryData['name'];
        $category->color = $categoryData['color'];

        $response = CouchDB::executeAuth('put', $this->database.$id, [
            'json' => $category
        ]);
        return $response;
    }

    public function allWithExpenses($date)
    {
        $categories = $this->all();
        $this->expensesTransformer->monthlyCategoriesExpenses($categories, $this->categoriesMap, $date);

        return $categories;
    }

    /**
     * @param \stdClass $category
     * @return array with transformed category
     */
    public function transform($category)
    {
        $categoryObj = new \stdClass();
        $categoryObj->_id = $category->doc->_id;
        $categoryObj->_rev = $category->doc->_rev;
        $categoryObj->type = $category->doc->type;
        $categoryObj->name = $category->doc->name;
        $categoryObj->color = $category->doc->color;
        $categoryObj->user_id = $category->doc->user_id;
        $categoryObj->expenses = [];
        $categoryObj->total = 0;

        return $categoryObj;
    }

    public function transformCollection(array $categories){
        $categoriesTransformed = [];
        foreach ($categories as $category)
        {
            if($category->doc->type == 'category'){
                $this->categoriesMap[$category->doc->_id] = count($this->categoriesMap);
                $category = $this->transform($category);
                $categoriesTransformed[] = $category;

            }
        }
        return $categoriesTransformed;
    }

    public function destroy($id)
    {
        $category = $this->find($id);

        $expenses = $this->expensesTransformer->categoryExpenses($id);
        if($expenses){
            foreach ($expenses as $key => $expense )
            {
                $expense->_deleted = true;
                $expenses[$key] = $expense;
            }

            $response = CouchDB::executeAuth('post',  $this->database.'_bulk_docs', [
                'json' => [
                    'docs' => $expenses
                ]
            ]);
        }

        $category->_deleted = true;

        $response = CouchDB::executeAuth('put', 'paka/'.$id, [
            'json' => $category
        ]);

        return $response;
    }

    public function seedCategories()
    {
        $user = CouchDB::getUser();


        $categories = [
            'docs' => [
                [
                    'type' => 'category',
                    'name'    => 'Health',
                    'color'   => '#AD242D',
                    'user_id' => $user->name,
                ],
                [
                    'type' => 'category',
                    'name'    => 'Food',
                    'color'   => '#BEE6CE',
                    'user_id' => $user->name,
                ],
                [
                    'type' => 'category',
                    'name'    => 'Transportation',
                    'color'   => '#D98D07',
                    'user_id' => $user->name,
                ],
                [
                    'type' => 'category',
                    'name'    => 'Leisure',
                    'color'   => '#21A179',
                    'user_id' => $user->name,
                ],
                [
                    'type' => 'category',
                    'name'    => 'Education',
                    'color'   => '#073B3A',
                    'user_id' => $user->name,
                ]
            ]
        ];

        $response = CouchDB::executeAuth('POST', 'paka/_bulk_docs', [
            'headers' => [
                'Content-Type'=> 'application/json'
            ],
            'json' => $categories
        ]);

        return $response;
    }

}