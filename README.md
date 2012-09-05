Nette Hierarchy
===============

Usage
-----

Nette forum model

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
     * @return Hierarchy 
     */
    public function getForums() {
        $forums = $this->database->table('forums')->order('root_id'); // No need to be ordered but it's faster
        /*$forums = $this->database->query('SELECT f.*,
                                                t.changed,
                                                COUNT(t.id) AS topics
                                         FROM ' . $this->dbPrefix . 'forums f
                                         LEFT JOIN (SELECT * 
                                                    FROM ' . $this->dbPrefix . 'topics
                                                    ORDER BY changed DESC) t ON t.forum_id=f.id
                                         GROUP BY f.id
                                         ORDER BY root_id, f.order');*/

        return new Tree\Hierarchy($forums, 'ForumNode');
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
