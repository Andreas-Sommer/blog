<?php

/*
 * This file is part of the package t3g/blog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace T3G\AgencyPack\Blog\Domain\Repository;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class TagRepository.
 */
class TagRepository extends Repository
{
	/**
	 * Plugin settings
	 *
	 * @var array $pluginSettings
	 */
	protected $pluginSettings;

    /**
     * Initializes the repository.
     *
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     * @throws \InvalidArgumentException
     */
    public function initializeObject()
    {
        $this->defaultOrderings = [
            'title' => QueryInterface::ORDER_ASCENDING,
        ];

		/** @var ConfigurationManagerInterface $configurationManager */
		$configurationManager = $this->objectManager->get(ConfigurationManagerInterface::class);
		$this->pluginSettings = $configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
		);
    }

    /**
     * @param int $limit
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \InvalidArgumentException
     */
    public function findTopByUsage($limit = 20)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_blog_domain_model_tag');
        $queryBuilder
            ->select('t.uid', 't.title')
            ->addSelectLiteral($queryBuilder->expr()->count('mm.uid_foreign', 'cnt'))
            ->from('tx_blog_domain_model_tag', 't')
            ->join('t', 'tx_blog_tag_pages_mm', 'mm', 'mm.uid_foreign = t.uid')
            ->groupBy('t.title', 't.uid')
            ->orderBy('cnt', 'DESC')
            ->setMaxResults($limit);

		// limitation to storage pid for multi domain purpose
		if($this->pluginSettings['storagePid'])
		{
			// force storage pids as integer
			$storagePids = GeneralUtility::intExplode(',', $this->pluginSettings['storagePid']);
			$queryBuilder->where('t.pid IN(' . implode(',', $storagePids) . ')');
		}

		$result = $queryBuilder
			->execute()
			->fetchAll();

        $rows = [];
        foreach ($result as $row) {
            $row['tagObject'] = $this->findByUid($row['uid']);
            $rows[] = $row;
        }
        // shuffle tags, ordering is only to get the top used tags
        shuffle($rows);
        return $rows;
    }
}
