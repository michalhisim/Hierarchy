<?php

namespace Tree;

/**
 * Nette Hierarchy
 *
 * @copyright Copyright (c) 2012 Michal Šimon
 */

use \Nette\Diagnostics\Debugger;

class Hierarchy extends \Nette\Object {

    private $data = NULL;
    private $unset = array();
    private $tree = array();
    private $nodeClass = 'HierarchyNode';
    private $maxLevel = 0;
    private $treeIterator = 0;

    function __construct(array $data, $nodeClass = 'HierarchyNode') {
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
                    $this->unset[] = $row;
                }
            }

            $count++;
        }


        if ($count > $this->maxLevel) {
            // first tree making
            $this->maxLevel = $count;
            $this->treeIterator++;
        }

        if (!empty($this->unset) AND ($this->treeIterator <= $this->maxLevel)) {
            $this->makeTree($this->unset); // todo Kontrola logiky, zda se nemůže rekurze zacyklit

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
     * Searching in tree
     * Lazy tree building.
     * @param int $id
     * @return HierarchyNode
     */
    public function findNode($id) {
        if (empty($this->tree)) {
            $this->makeTree();
        }

        foreach ($this->tree AS $child) {
            if ($result = $child->findChild($id)) {
                return $result;
            }
        }
    }

    /**
     * Generate array of node IDs as path to node
     * Lazy tree building.
     * @param int $id
     * @return array of IDs
     */    
    public function getPathTo($id) {
        if (empty($this->tree)) {
            $this->makeTree();
        }

        foreach ($this->tree AS $node) {
            $path = array($node->id);

            if ($output = $node->getPathTo($id)) {
                
                $path = array_merge($path, $output);

                return $path;
            }
        }
    }

}
