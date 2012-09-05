Nette Hierarchy
===============

Usage
-----

``` php
use \Nette\Diagnostics\Debugger,
    \Nette\Database\Connection;

class ForumModel extends Nette\Object {

    /** @var \Nette\Database\Connection */
    private $database;    

    public function __construct(Connection $db) {
        $this->database = $db;
    }

    /**
     * Load forums from database.
     * @return ("tree") array or NULL 
     */
    public function getForums() {
        $forums = $this->database->table('forums');

        return new Hierarchy($forums, 'ForumNode');
    }
}

class ForumNode extends HierarchyNode {

    public $topics = NULL;
    public $changed = NULL;

    function __construct($info) {

        parent::__construct($info);

        $this->topics = $info->topics;
        $this->changed = $info->changed;
    }
}
```
