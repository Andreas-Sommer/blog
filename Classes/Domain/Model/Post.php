<?php

/*
 * This file is part of the package t3g/blog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace T3G\AgencyPack\Blog\Domain\Model;

use T3G\AgencyPack\Blog\Constants;
use T3G\AgencyPack\Blog\Domain\Repository\CommentRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Post.
 */
class Post extends AbstractEntity
{
    /**
     * The blog post doktype
     *
     * @var int
     */
    protected $doktype = Constants::DOKTYPE_BLOG_POST;

    /**
     * The blog post title.
     *
     * @var string
     */
    protected $title;

    /**
     * The blog post subtitle.
     *
     * @var string
     */
    protected $subtitle;

    /**
     * The blog post abstract (SEO, list if not empty).
     *
     * @var string
     */
    protected $abstract;

    /**
     * The blog post description (SEO, list if not empty).
     *
     * @var string
     */
    protected $description;

    /**
     * Thie blog post author.
     *
     * @var string
     *
     * @deprecated since EXT:blog v1.2.0, this property will be removed in EXT:blog v2.0.0
     */
    protected $author;

    /**
     * The blog post creation date.
     *
     * @var \DateTime
     */
    protected $crdate;

    /**
     * The blog post categories.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\T3G\AgencyPack\Blog\Domain\Model\Category>
     * @lazy
     */
    protected $categories;

    /**
     * Comments active flag for this blog post.
     *
     * @var bool
     */
    protected $commentsActive;

    /**
     * Comments of the blog post.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\T3G\AgencyPack\Blog\Domain\Model\Comment>
     * @lazy
     */
    protected $comments;

    /**
     * Tags of the blog post.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\T3G\AgencyPack\Blog\Domain\Model\Tag>
     * @lazy
     */
    protected $tags;

    /**
     * Sharing enabled flag for this blog post. This flag can be used in views to enable sharing tools.
     *
     * @var bool
     */
    protected $sharingEnabled;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     * @lazy
     */
    protected $media;

    /**
     * @var int
     */
    protected $archiveDate;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\T3G\AgencyPack\Blog\Domain\Model\Author>
     * @lazy
     */
    protected $authors;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->initializeObject();
    }

    /**
     * initializeObject
     */
    public function initializeObject()
    {
        $this->categories = new ObjectStorage();
        $this->comments = new ObjectStorage();
        $this->tags = new ObjectStorage();
        $this->authors = new ObjectStorage();
        $this->media = new ObjectStorage();
    }

    /**
     * @return int
     */
    public function getDoktype()
    {
        return $this->doktype;
    }

    /**
     * @param Author $author
     */
    public function addAuthor(Author $author)
    {
        $this->authors->attach($author);
    }

    /**
     * @param Author $author
     */
    public function removeAuthor(Author $author)
    {
        $this->authors->detach($author);
    }

    /**
     * @return ObjectStorage
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @param ObjectStorage $authors
     */
    public function setAuthors(ObjectStorage $authors)
    {
        $this->authors = $authors;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     *
     * @return $this
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @param string $abstract
     *
     * @return $this
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param ObjectStorage $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param Category $category
     *
     * @return $this
     */
    public function addCategory(Category $category)
    {
        $this->categories->attach($category);

        return $this;
    }

    /**
     * @param Category $category
     *
     * @return $this
     */
    public function removeCategory(Category $category)
    {
        $this->categories->detach($category);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * @param \DateTime $crdate
     *
     * @return $this
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCommentsActive()
    {
        return $this->commentsActive;
    }

    /**
     * @param bool $commentsActive
     */
    public function setCommentsActive($commentsActive)
    {
        $this->commentsActive = $commentsActive;
    }

    /**
     * @return ObjectStorage
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return ObjectStorage
     */
    public function getActiveComments()
    {
        return GeneralUtility::makeInstance(ObjectManager::class)
            ->get(CommentRepository::class)
            ->findAllByPost($this);
    }

    /**
     * @param ObjectStorage $comments
     *
     * @return $this
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @param Comment $comment
     *
     * @return $this
     */
    public function addComment(Comment $comment)
    {
        $this->comments->attach($comment);

        return $this;
    }

    /**
     * @param Comment $comment
     *
     * @return $this
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->detach($comment);

        return $this;
    }

    /**
     * @return bool
     */
    public function isSharingEnabled()
    {
        return $this->sharingEnabled;
    }

    /**
     * @param bool $sharingEnabled
     *
     * @return $this
     */
    public function setSharingEnabled($sharingEnabled)
    {
        $this->sharingEnabled = (bool) $sharingEnabled;

        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param ObjectStorage $tags
     *
     * @return $this
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function addTag(Tag $tag)
    {
        $this->tags->attach($tag);

        return $this;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->detach($tag);

        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getMedia()
    {
        return $this->media;
    }

	/**
	 * removes all media file references
	 */
    public function removeAllMedia()
	{
		$this->media->removeAll($this->getMedia());
	}

    /**
	 * Adds a media file reference
	 *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $media
     */
    public function addMedia($media)
    {
        $this->media->attach($media);
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $media
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * @return int
     */
    public function getArchiveDate()
    {
        return $this->archiveDate;
    }

    /**
     * @param int $archiveDate
     */
    public function setArchiveDate($archiveDate)
    {
        $this->archiveDate = $archiveDate;
    }

    /**
     * @return string
     *
     * @deprecated since EXT:blog v1.2.0, this method will be removed in EXT:blog v2.0.0
     */
    public function getAuthor()
    {
        GeneralUtility::logDeprecatedFunction();

        return $this->author;
    }

    /**
     * @param string $author
     *
     * @deprecated since EXT:blog v1.2.0, this method will be removed in EXT:blog v2.0.0
     */
    public function setAuthor($author)
    {
        GeneralUtility::logDeprecatedFunction();
        $this->author = $author;
    }
}
