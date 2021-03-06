<?php

declare(strict_types=1);

/*
 * This file is a part of Sculpin.
 *
 * (c) Dragonfly Development Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sculpin\Core\DataProvider;

/**
 * Data Provider Manager.
 *
 * @author Beau Simensen <beau@dflydev.com>
 */
class DataProviderManager
{
    /**
     * Data providers
     *
     * @var array
     */
    protected $dataProviders = [];

    /**
     * Register data provider
     *
     * @param string                $name         Name
     * @param DataProviderInterface $dataProvider Data provider
     */
    public function registerDataProvider(string $name, DataProviderInterface $dataProvider)
    {
        $this->dataProviders[$name] = $dataProvider;
    }

    /**
     * List of registered ata provider names
     *
     * @return array
     */
    public function dataProviders(): array
    {
        return array_keys($this->dataProviders);
    }

    /**
     * Data provider
     *
     * @param string $name Name
     *
     * @return DataProviderInterface
     */
    public function dataProvider(string $name): DataProviderInterface
    {
        return $this->dataProviders[$name];
    }
}
