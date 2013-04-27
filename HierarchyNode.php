<?php

namespace Tree;

/**
 * Nette Hierarchy
 *
 * @copyright Copyright (c) 2012 Michal Šimon
 */
use \Nette\Diagnostics\Debugger;

class Hierarchy extends \Nette\Object {

    protected $data = NULL;
    protected $notSet = array();
    protected $tree = array();
    protected $nodeClass = 'HierarchyNode';
    protected $maxLevel = 0;
    protected $treeIterator = 0;

    function __construct(\Nette\Database\Statement $data, $nodeClass = 'HierarchyNode') {
        $this->data = $data;
        $this->nodeClass = $nodeClass;
    }

    /**
     * Build a tree from data "table".
     * @return tree array 
     */
    protected function makeTree($data = NULL) {
        $list = NULL;

        if ($data != NULL) {
            $list = $data;
        } else {
            $list = $this->data;
        }

        $count = 0;

        foreach ($list AS $row) {
            $node = new $this->nodeClass($row);

            if ($node->rootId == 0) {
                // Level 0
                $this->tree[$node->id] = $node;
            } elseif (isset($this->tree[$node->rootId])) {
                // Level 1
                $this->tree[$node->rootId]->addChild($node);
            } else {
                // SubLevels
                $added = false;

                foreach ($this->tree AS $nodeRow) {
                    if ($nodeRow->addChild($node)) {
                        $added = true;
                        break;
                    }
                }

                if ($added == false) {
                    $this->notSet[] = $row;
                }
            }

            $count++;
        }

        if ($count > $this->maxLevel) {
            // first tree making
            $this->maxLevel = $count;
            $this->treeIterator++;
        }

        if (!empty($this->notSet) AND ($this->treeIterator <= $this->maxLevel)) {
            $this->makeTree($this->notSet); // todo Kontrola logiky, zda se nemůže rekurze zacyklit

            $this->treeIterator++;
        }

        return $this->tree;
    }

    /**
     * Tree getter
     * Lazy tree building.
     * @return tree array 
     */
    public function getTree() {
        if (empty($this->tree)) {
            $this->makeTree();
        }

        return $this->tree;
    }

    /**
     * Tree list getter
     * Lazy tree building.
     * @return tree array 
     */
    public function getList($tree = NULL) { // todo pomalé

        if ($tree == NULL) {
            $tree = $this->getTree();
        }

        $list = array();

        foreach ($tree as $node) {

            $list[$node->id] = $node;
            if (isset($node->children)) {
                
                $result = $this->getList($node->children); // pomocí array_merge() se rozhází poředí
                
                foreach($result AS $child)
                {
                    $list[$child->id] = $child;
                }
            }
        }

        return $list;
    }

    /**
     * Searching in tree
     * Lazy tree building.
     * @param int $id
     * @return HierarchyNode
     */
    public function findNode($id) {

        foreach ($this->getTree() AS $child) {
            if ($result = $child->findChild($id)) {
                return $result;
            }
        }
        
        return False;
    }

    /**
     * Generate array of node IDs as path to node
     * Lazy tree building.
     * @param int $id
     * @return array
     */
    public function getPathTo($id) {

        foreach ($this->getTree() AS $node) {
            $path = array($node);

            if ($output = $node->getPathTo($id)) {

                $path = array_merge($path, $output);

                return $path;
            }
        }
        
        return False;
    }
    
    /**
     * Generate list of subNodes IDs
     * Lazy tree building.
     * @param int $id Node ID
     * @return array 
     */
    public function getSubIds($id) {
        $node = $this->findNode($id);
        
        return $node->getSubIds();
    }

}
