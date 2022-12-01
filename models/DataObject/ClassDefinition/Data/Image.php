<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Model\DataObject\ClassDefinition\Data;

use Pimcore\Model;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element;
use Pimcore\Normalizer\NormalizerInterface;

class Image extends Data implements ResourcePersistenceAwareInterface, QueryResourcePersistenceAwareInterface, TypeDeclarationSupportInterface, EqualComparisonInterface, VarExporterInterface, NormalizerInterface, IdRewriterInterface
{
    use Extension\ColumnType;
    use ImageTrait;
    use Extension\QueryColumnType;
    use Data\Extension\RelationFilterConditionParser;

    /**
     * Static type of this element
     *
     * @internal
     *
     * @var string
     */
    public string $fieldtype = 'image';

    /**
     * Type for the column to query
     *
     * @internal
     *
     * @var string
     */
    public $queryColumnType = 'int(11)';

    /**
     * Type for the column
     *
     * @internal
     *
     * @var string
     */
    public $columnType = 'int(11)';

    /**
     * @param mixed $data
     * @param null|Model\DataObject\Concrete $object
     * @param array $params
     *
     * @return int|null
     *
     * @see ResourcePersistenceAwareInterface::getDataForResource
     *
     */
    public function getDataForResource(mixed $data, DataObject\Concrete $object = null, array $params = []): ?int
    {
        if ($data instanceof Asset\Image) {
            return $data->getId();
        }

        return null;
    }

    /**
     * @param mixed $data
     * @param null|Model\DataObject\Concrete $object
     * @param array $params
     *
     * @return Asset|null
     *
     *@see ResourcePersistenceAwareInterface::getDataFromResource
     *
     */
    public function getDataFromResource(mixed $data, DataObject\Concrete $object = null, array $params = []): ?Asset
    {
        if ((int)$data > 0) {
            return Asset\Image::getById($data);
        }

        return null;
    }

    /**
     * @param mixed $data
     * @param null|Model\DataObject\Concrete $object
     * @param array $params
     *
     * @return int|null
     *
     *@see QueryResourcePersistenceAwareInterface::getDataForQueryResource
     *
     */
    public function getDataForQueryResource(mixed $data, DataObject\Concrete $object = null, array $params = []): ?int
    {
        if ($data instanceof Asset\Image) {
            return $data->getId();
        }

        return null;
    }

    /**
     * @param mixed $data
     * @param Concrete|null $object
     * @param array $params
     *
     * @return array|null
     *
     * @see Data::getDataForEditmode
     */
    public function getDataForEditmode(mixed $data, Concrete $object = null, array $params = []): ?array
    {
        if ($data instanceof Asset\Image) {
            return $data->getObjectVars();
        }

        return null;
    }

    /**
     * @param Asset\Image $data
     * @param null|Model\DataObject\Concrete $object
     * @param array $params
     *
     * @return array|null
     */
    public function getDataForGrid(?Asset\Image $data, Concrete $object = null, array $params = []): ?array
    {
        return $this->getDataForEditmode($data, $object, $params);
    }

    /**
     * @param mixed $data
     * @param null|Model\DataObject\Concrete $object
     * @param array $params
     *
     * @return Asset\Image|null
     *
     *@see Data::getDataFromEditmode
     *
     */
    public function getDataFromEditmode(mixed $data, DataObject\Concrete $object = null, array $params = []): ?Asset\Image
    {
        if ($data && (int)$data['id'] > 0) {
            return Asset\Image::getById($data['id']);
        }

        return null;
    }

    /**
     * @param mixed $data
     * @param bool $omitMandatoryCheck
     * @param array $params
     *
     * @throws Element\ValidationException
     */
    public function checkValidity(mixed $data, bool $omitMandatoryCheck = false, array $params = [])
    {
        if (!$omitMandatoryCheck && $this->getMandatory() && !$data instanceof Asset\Image) {
            throw new Element\ValidationException('Empty mandatory field [ '.$this->getName().' ]');
        }
        if ($data !== null && !$data instanceof Asset\Image) {
            throw new Element\ValidationException('Invalid data in field `'.$this->getName().'`');
        }
    }

    /**
     * @param array|null $data
     * @param null|Model\DataObject\Concrete $object
     * @param array $params
     *
     * @return Asset\Image|null
     */
    public function getDataFromGridEditor(?array $data, Concrete $object = null, array $params = []): Asset\Image|null
    {
        return $this->getDataFromEditmode($data, $object, $params);
    }

    /**
     * @param mixed $data
     * @param null|Model\DataObject\Concrete $object
     * @param array $params
     *
     * @return string
     *
     * @see Data::getVersionPreview
     *
     */
    public function getVersionPreview(mixed $data, DataObject\Concrete $object = null, array $params = []): string
    {
        if ($data instanceof Asset\Image) {
            return '<img src="/admin/asset/get-image-thumbnail?id=' . $data->getId() . '&width=100&height=100&aspectratio=true" />';
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getForCsvExport(DataObject\Localizedfield|DataObject\Fieldcollection\Data\AbstractData|DataObject\Objectbrick\Data\AbstractData|DataObject\Concrete $object, array $params = []): string
    {
        $data = $this->getDataFromObjectParam($object, $params);
        if ($data instanceof Element\ElementInterface) {
            return $data->getRealFullPath();
        }

        return '';
    }

    public function getDataForSearchIndex(DataObject\Localizedfield|DataObject\Fieldcollection\Data\AbstractData|DataObject\Objectbrick\Data\AbstractData|DataObject\Concrete $object, array $params = []): string
    {
        return '';
    }

    public function getCacheTags(mixed $data, array $tags = []): array
    {
        if ($data instanceof Asset\Image) {
            if (!array_key_exists($data->getCacheTag(), $tags)) {
                $tags = $data->getCacheTags($tags);
            }
        }

        return $tags;
    }

    public function resolveDependencies(mixed $data): array
    {
        $dependencies = [];

        if ($data instanceof Asset) {
            $dependencies['asset_' . $data->getId()] = [
                'id' => $data->getId(),
                'type' => 'asset',
            ];
        }

        return $dependencies;
    }

    /**
     * {@inheritdoc}
     */
    public function isDiffChangeAllowed(Concrete $object, array $params = []): bool
    {
        return true;
    }

    /** Generates a pretty version preview (similar to getVersionPreview) can be either html or
     * a image URL. See the https://github.com/pimcore/object-merger bundle documentation for details
     *
     * @param Asset\Image|null $data
     * @param Model\DataObject\Concrete|null $object
     * @param array $params
     *
     * @return array|string
     */
    public function getDiffVersionPreview(?Asset\Image $data, Concrete $object = null, array $params = []): array|string
    {
        $versionPreview = null;
        if ($data instanceof Asset\Image) {
            $versionPreview = '/admin/asset/get-image-thumbnail?id=' . $data->getId() . '&width=150&height=150&aspectratio=true';
        }

        if ($versionPreview) {
            $value = [];
            $value['src'] = $versionPreview;
            $value['type'] = 'img';

            return $value;
        } else {
            return '';
        }
    }

    /**
     * { @inheritdoc }
     */
    public function rewriteIds(mixed $container, array $idMapping, array $params = []): mixed
    {
        $data = $this->getDataFromObjectParam($container, $params);
        if ($data instanceof Asset\Image) {
            if (array_key_exists('asset', $idMapping) && array_key_exists($data->getId(), $idMapping['asset'])) {
                return Asset::getById($idMapping['asset'][$data->getId()]);
            }
        }

        return $data;
    }

    /**
     * @param Model\DataObject\ClassDefinition\Data\Image $masterDefinition
     */
    public function synchronizeWithMasterDefinition(Model\DataObject\ClassDefinition\Data $masterDefinition)
    {
        $this->uploadPath = $masterDefinition->uploadPath;
    }

    /**
     * {@inheritdoc}
     */
    public function isFilterable(): bool
    {
        return true;
    }

    public function isEqual(mixed $oldValue, mixed $newValue): bool
    {
        $oldValue = $oldValue instanceof Asset ? $oldValue->getId() : null;
        $newValue = $newValue instanceof Asset ? $newValue->getId() : null;

        return $oldValue === $newValue;
    }

    public function getParameterTypeDeclaration(): ?string
    {
        return '?\\' . Asset\Image::class;
    }

    public function getReturnTypeDeclaration(): ?string
    {
        return '?\\' . Asset\Image::class;
    }

    public function getPhpdocInputType(): ?string
    {
        return '\\' . Asset\Image::class . '|null';
    }

    public function getPhpdocReturnType(): ?string
    {
        return '\\' . Asset\Image::class . '|null';
    }

    public function normalize(mixed $value, array $params = []): ?array
    {
        if ($value instanceof \Pimcore\Model\Asset\Image) {
            return [
                'type' => 'asset',
                'id' => $value->getId(),
            ];
        }

        return null;
    }

    public function denormalize(mixed $value, array $params = []): ?Asset
    {
        if (isset($value['id'])) {
            return Asset\Image::getById($value['id']);
        }

        return null;
    }

    /**
     * Filter by relation feature
     *
     * @param mixed $value
     * @param string $operator
     * @param array $params
     *
     * @return string
     */
    public function getFilterConditionExt(mixed $value, string $operator, array $params = []): string
    {
        $name = $params['name'] ?: $this->name;

        return $this->getRelationFilterCondition($value, $operator, $name);
    }
}
