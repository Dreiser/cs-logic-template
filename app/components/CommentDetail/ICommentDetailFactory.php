<?php

namespace App\Components\CommentDetail;

/**
 * Interface ICommentDetailFactory
 * @package App\Components\CommentDetail
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
interface ICommentDetailFactory
{
    /**
     * @return CommentDetail
     */
    public function create();
}
