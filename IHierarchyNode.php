<?php

namespace Tree;

/**
 * IHierarchyNode
 *
 * @copyright Copyright (c) 2012 Michal Šimon
 */
interface IHierarchyNode {
    
    /**
     * @return bool
     */
    public function addChild(IHierarchyNode $child);

    /**
     * @return HierarchyNode | false
     */
    public function findChild($id);

    /**
     * @return array
     */
    public function getPathTo($id);
}