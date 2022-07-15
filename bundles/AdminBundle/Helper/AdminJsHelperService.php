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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http:/ /www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\AdminBundle\Helper;

use Pimcore\Localization\LocaleService;
use Pimcore\Bundle\AdminBundle\Security\ContentSecurityPolicyHandler;
use Pimcore\Extension\Bundle\PimcoreBundleManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Pimcore\Version;

/**
 * @internal
 */
class AdminJsHelperService implements WarmableInterface
{

    /**
     * @var string[]
     */
    protected $libScriptPaths = [
        "lib/class.js",
        "../extjs/js/ext-all.js",
        "lib/ext-plugins/portlet/PortalDropZone.js",
        "lib/ext-plugins/portlet/Portlet.js",
        "lib/ext-plugins/portlet/PortalColumn.js",
        "lib/ext-plugins/portlet/PortalPanel.js",
        "lib/ckeditor/ckeditor.js",
        "lib/leaflet/leaflet.js",
        "lib/leaflet.draw/leaflet.draw.js",
        "lib/vrview/build/vrview.min.js",
        "lib/ace/src-min-noconflict/ace.js",
        "lib/ace/src-min-noconflict/ext-modelist.js"
    ];

    /**
     * @var string[]
     */
    protected $internalScriptPaths = [
        "pimcore/functions.js",
        "pimcore/common.js",
        "pimcore/elementservice.js",
        "pimcore/helpers.js",
        "pimcore/error.js",
        "pimcore/events.js",

        "pimcore/treenodelocator.js",
        "pimcore/helpers/generic-grid.js",
        "pimcore/helpers/quantityValue.js",
        "pimcore/overrides.js",

        "pimcore/perspective.js",
        "pimcore/user.js",


        "pimcore/tool/paralleljobs.js",
        "pimcore/tool/genericiframewindow.js",


        "pimcore/settings/user/panels/abstract.js",
        "pimcore/settings/user/panel.js",

        "pimcore/settings/user/usertab.js",
        "pimcore/settings/user/editorSettings.js",
        "pimcore/settings/user/websiteTranslationSettings.js",
        "pimcore/settings/user/role/panel.js",
        "pimcore/settings/user/role/tab.js",
        "pimcore/settings/user/user/objectrelations.js",
        "pimcore/settings/user/user/settings.js",
        "pimcore/settings/user/user/keyBindings.js",
        "pimcore/settings/user/workspaces.js",
        "pimcore/settings/user/workspace/asset.js",
        "pimcore/settings/user/workspace/document.js",
        "pimcore/settings/user/workspace/object.js",
        "pimcore/settings/user/workspace/customlayouts.js",
        "pimcore/settings/user/workspace/language.js",
        "pimcore/settings/user/workspace/special.js",
        "pimcore/settings/user/role/settings.js",
        "pimcore/settings/profile/panel.js",
        "pimcore/settings/profile/twoFactorSettings.js",
        "pimcore/settings/thumbnail/item.js",
        "pimcore/settings/thumbnail/panel.js",
        "pimcore/settings/videothumbnail/item.js",
        "pimcore/settings/videothumbnail/panel.js",
        "pimcore/settings/translation.js",
        "pimcore/settings/translationEditor.js",
        "pimcore/settings/translation/translationmerger.js",
        "pimcore/settings/translation/xliff.js",
        "pimcore/settings/translation/word.js",
        "pimcore/settings/translation/translationSettingsTab.js",
        "pimcore/settings/metadata/predefined.js",
        "pimcore/settings/properties/predefined.js",
        "pimcore/settings/docTypes.js",
        "pimcore/settings/system.js",
        "pimcore/settings/web2print.js",
        "pimcore/settings/website.js",
        "pimcore/settings/staticroutes.js",
        "pimcore/settings/redirects.js",
        "pimcore/settings/glossary.js",
        "pimcore/settings/recyclebin.js",
        "pimcore/settings/fileexplorer/file.js",
        "pimcore/settings/fileexplorer/explorer.js",
        "pimcore/settings/maintenance.js",
        "pimcore/settings/robotstxt.js",
        "pimcore/settings/httpErrorLog.js",
        "pimcore/settings/email/log.js",
        "pimcore/settings/email/blacklist.js",
        "pimcore/settings/targeting/condition/abstract.js",
        "pimcore/settings/targeting/conditions.js",
        "pimcore/settings/targeting/action/abstract.js",
        "pimcore/settings/targeting/actions.js",
        "pimcore/settings/targeting/rules/panel.js",
        "pimcore/settings/targeting/rules/item.js",
        "pimcore/settings/targeting/targetGroups/panel.js",
        "pimcore/settings/targeting/targetGroups/item.js",
        "pimcore/settings/targeting_toolbar.js",

        "pimcore/settings/gdpr/gdprPanel.js",
        "pimcore/settings/gdpr/dataproviders/assets.js",
        "pimcore/settings/gdpr/dataproviders/dataObjects.js",
        "pimcore/settings/gdpr/dataproviders/sentMail.js",
        "pimcore/settings/gdpr/dataproviders/pimcoreUsers.js",


        "pimcore/element/abstract.js",
        "pimcore/element/abstractPreview.js",
        "pimcore/element/selector/selector.js",
        "pimcore/element/selector/abstract.js",
        "pimcore/element/selector/document.js",
        "pimcore/element/selector/asset.js",
        "pimcore/element/properties.js",
        "pimcore/element/scheduler.js",
        "pimcore/element/dependencies.js",
        "pimcore/element/metainfo.js",
        "pimcore/element/history.js",
        "pimcore/element/notes.js",
        "pimcore/element/note_details.js",
        "pimcore/element/workflows.js",
        "pimcore/element/tag/imagecropper.js",
        "pimcore/element/tag/imagehotspotmarkereditor.js",
        "pimcore/element/replace_assignments.js",
        "pimcore/element/permissionchecker.js",
        "pimcore/element/gridexport/abstract.js",
        "pimcore/element/helpers/gridColumnConfig.js",
        "pimcore/element/helpers/gridConfigDialog.js",
        "pimcore/element/helpers/gridCellEditor.js",
        "pimcore/element/helpers/gridTabAbstract.js",
        "pimcore/object/helpers/grid.js",
        "pimcore/object/helpers/gridConfigDialog.js",
        "pimcore/object/helpers/classTree.js",
        "pimcore/object/helpers/gridTabAbstract.js",
        "pimcore/object/helpers/metadataMultiselectEditor.js",
        "pimcore/object/helpers/customLayoutEditor.js",
        "pimcore/object/helpers/optionEditor.js",
        "pimcore/object/helpers/imageGalleryDropZone.js",
        "pimcore/object/helpers/imageGalleryPanel.js",
        "pimcore/element/selector/object.js",
        "pimcore/element/tag/configuration.js",
        "pimcore/element/tag/assignment.js",
        "pimcore/element/tag/tree.js",
        "pimcore/asset/helpers/metadataTree.js",
        "pimcore/asset/helpers/gridConfigDialog.js",
        "pimcore/asset/helpers/gridTabAbstract.js",
        "pimcore/asset/helpers/grid.js",


        "pimcore/document/properties.js",
        "pimcore/document/document.js",
        "pimcore/document/page_snippet.js",
        "pimcore/document/edit.js",
        "pimcore/document/versions.js",
        "pimcore/document/settings_abstract.js",
        "pimcore/document/pages/settings.js",
        "pimcore/document/pages/preview.js",
        "pimcore/document/snippets/settings.js",
        "pimcore/document/emails/settings.js",
        "pimcore/document/newsletters/settings.js",
        "pimcore/document/newsletters/sendingPanel.js",
        "pimcore/document/newsletters/plaintextPanel.js",
        "pimcore/document/newsletters/addressSourceAdapters/default.js",
        "pimcore/document/newsletters/addressSourceAdapters/csvList.js",
        "pimcore/document/newsletters/addressSourceAdapters/report.js",
        "pimcore/document/link.js",
        "pimcore/document/hardlink.js",
        "pimcore/document/folder.js",
        "pimcore/document/tree.js",
        "pimcore/document/snippet.js",
        "pimcore/document/email.js",
        "pimcore/document/newsletter.js",
        "pimcore/document/page.js",
        "pimcore/document/printpages/pdf_preview.js",
        "pimcore/document/printabstract.js",
        "pimcore/document/printpage.js",
        "pimcore/document/printcontainer.js",
        "pimcore/document/seopanel.js",
        "pimcore/document/document_language_overview.js",
        "pimcore/document/customviews/tree.js",


        "pimcore/asset/metadata/data/data.js",
        "pimcore/asset/metadata/data/input.js",
        "pimcore/asset/metadata/data/textarea.js",
        "pimcore/asset/metadata/data/asset.js",
        "pimcore/asset/metadata/data/document.js",
        "pimcore/asset/metadata/data/object.js",
        "pimcore/asset/metadata/data/date.js",
        "pimcore/asset/metadata/data/checkbox.js",
        "pimcore/asset/metadata/data/select.js",

        "pimcore/asset/metadata/tags/abstract.js",
        "pimcore/asset/metadata/tags/checkbox.js",
        "pimcore/asset/metadata/tags/date.js",
        "pimcore/asset/metadata/tags/input.js",
        "pimcore/asset/metadata/tags/manyToOneRelation.js",
        "pimcore/asset/metadata/tags/asset.js",
        "pimcore/asset/metadata/tags/document.js",
        "pimcore/asset/metadata/tags/object.js",
        "pimcore/asset/metadata/tags/select.js",
        "pimcore/asset/metadata/tags/textarea.js",
        "pimcore/asset/asset.js",
        "pimcore/asset/unknown.js",
        "pimcore/asset/embedded_meta_data.js",
        "pimcore/asset/image.js",
        "pimcore/asset/document.js",
        "pimcore/asset/archive.js",
        "pimcore/asset/video.js",
        "pimcore/asset/audio.js",
        "pimcore/asset/text.js",
        "pimcore/asset/folder.js",
        "pimcore/asset/listfolder.js",
        "pimcore/asset/versions.js",
        "pimcore/asset/metadata/dataProvider.js",
        "pimcore/asset/metadata/grid.js",
        "pimcore/asset/metadata/editor.js",
        "pimcore/asset/tree.js",
        "pimcore/asset/customviews/tree.js",
        "pimcore/asset/gridexport/csv.js",
        "pimcore/asset/gridexport/xlsx.js",


        "pimcore/object/helpers/edit.js",
        "pimcore/object/helpers/layout.js",
        "pimcore/object/classes/class.js",
        "pimcore/object/class.js",
        "pimcore/object/bulk-base.js",
        "pimcore/object/bulk-export.js",
        "pimcore/object/bulk-import.js",
        "pimcore/object/classes/data/data.js",
        "pimcore/object/classes/data/block.js",
        "pimcore/object/classes/data/classificationstore.js",
        "pimcore/object/classes/data/rgbaColor.js",
        "pimcore/object/classes/data/date.js",
        "pimcore/object/classes/data/datetime.js",
        "pimcore/object/classes/data/encryptedField.js",
        "pimcore/object/classes/data/time.js",
        "pimcore/object/classes/data/manyToOneRelation.js",
        "pimcore/object/classes/data/image.js",
        "pimcore/object/classes/data/externalImage.js",
        "pimcore/object/classes/data/hotspotimage.js",
        "pimcore/object/classes/data/imagegallery.js",
        "pimcore/object/classes/data/video.js",
        "pimcore/object/classes/data/input.js",
        "pimcore/object/classes/data/numeric.js",
        "pimcore/object/classes/data/manyToManyObjectRelation.js",
        "pimcore/object/classes/data/advancedManyToManyRelation.js",
        "pimcore/object/classes/data/advancedManyToManyObjectRelation.js",
        "pimcore/object/classes/data/reverseObjectRelation.js",
        "pimcore/object/classes/data/booleanSelect.js",
        "pimcore/object/classes/data/select.js",
        "pimcore/object/classes/data/urlSlug.js",
        "pimcore/object/classes/data/user.js",
        "pimcore/object/classes/data/textarea.js",
        "pimcore/object/classes/data/wysiwyg.js",
        "pimcore/object/classes/data/checkbox.js",
        "pimcore/object/classes/data/consent.js",
        "pimcore/object/classes/data/slider.js",
        "pimcore/object/classes/data/manyToManyRelation.js",
        "pimcore/object/classes/data/table.js",
        "pimcore/object/classes/data/structuredTable.js",
        "pimcore/object/classes/data/country.js",
        "pimcore/object/classes/data/geo/abstract.js",
        "pimcore/object/classes/data/geopoint.js",
        "pimcore/object/classes/data/geobounds.js",
        "pimcore/object/classes/data/geopolygon.js",
        "pimcore/object/classes/data/geopolyline.js",
        "pimcore/object/classes/data/language.js",
        "pimcore/object/classes/data/password.js",
        "pimcore/object/classes/data/multiselect.js",
        "pimcore/object/classes/data/link.js",
        "pimcore/object/classes/data/fieldcollections.js",
        "pimcore/object/classes/data/objectbricks.js",
        "pimcore/object/classes/data/localizedfields.js",
        "pimcore/object/classes/data/countrymultiselect.js",
        "pimcore/object/classes/data/languagemultiselect.js",
        "pimcore/object/classes/data/firstname.js",
        "pimcore/object/classes/data/lastname.js",
        "pimcore/object/classes/data/email.js",
        "pimcore/object/classes/data/gender.js",
        "pimcore/object/classes/data/newsletterActive.js",
        "pimcore/object/classes/data/newsletterConfirmed.js",
        "pimcore/object/classes/data/targetGroup.js",
        "pimcore/object/classes/data/targetGroupMultiselect.js",
        "pimcore/object/classes/data/quantityValue.js",
        "pimcore/object/classes/data/inputQuantityValue.js",
        "pimcore/object/classes/data/calculatedValue.js",
        "pimcore/object/classes/layout/layout.js",
        "pimcore/object/classes/layout/accordion.js",
        "pimcore/object/classes/layout/fieldset.js",
        "pimcore/object/classes/layout/fieldcontainer.js",
        "pimcore/object/classes/layout/panel.js",
        "pimcore/object/classes/layout/region.js",
        "pimcore/object/classes/layout/tabpanel.js",
        "pimcore/object/classes/layout/button.js",
        "pimcore/object/classes/layout/iframe.js",
        "pimcore/object/fieldlookup/filterdialog.js",
        "pimcore/object/fieldlookup/helper.js",
        "pimcore/object/classes/layout/text.js",
        "pimcore/object/fieldcollection.js",
        "pimcore/object/fieldcollections/field.js",
        "pimcore/object/gridcolumn/Abstract.js",
        "pimcore/object/gridcolumn/operator/IsEqual.js",
        "pimcore/object/gridcolumn/operator/Text.js",
        "pimcore/object/gridcolumn/operator/Anonymizer.js",
        "pimcore/object/gridcolumn/operator/AnyGetter.js",
        "pimcore/object/gridcolumn/operator/AssetMetadataGetter.js",
        "pimcore/object/gridcolumn/operator/Arithmetic.js",
        "pimcore/object/gridcolumn/operator/Boolean.js",
        "pimcore/object/gridcolumn/operator/BooleanFormatter.js",
        "pimcore/object/gridcolumn/operator/CaseConverter.js",
        "pimcore/object/gridcolumn/operator/CharCounter.js",
        "pimcore/object/gridcolumn/operator/Concatenator.js",
        "pimcore/object/gridcolumn/operator/DateFormatter.js",
        "pimcore/object/gridcolumn/operator/ElementCounter.js",
        "pimcore/object/gridcolumn/operator/Iterator.js",
        "pimcore/object/gridcolumn/operator/JSON.js",
        "pimcore/object/gridcolumn/operator/LocaleSwitcher.js",
        "pimcore/object/gridcolumn/operator/Merge.js",
        "pimcore/object/gridcolumn/operator/ObjectFieldGetter.js",
        "pimcore/object/gridcolumn/operator/PHP.js",
        "pimcore/object/gridcolumn/operator/PHPCode.js",
        "pimcore/object/gridcolumn/operator/Base64.js",
        "pimcore/object/gridcolumn/operator/TranslateValue.js",
        "pimcore/object/gridcolumn/operator/PropertyGetter.js",
        "pimcore/object/gridcolumn/operator/RequiredBy.js",
        "pimcore/object/gridcolumn/operator/StringContains.js",
        "pimcore/object/gridcolumn/operator/StringReplace.js",
        "pimcore/object/gridcolumn/operator/Substring.js",
        "pimcore/object/gridcolumn/operator/LFExpander.js",
        "pimcore/object/gridcolumn/operator/Trimmer.js",
        "pimcore/object/gridcolumn/operator/Alias.js",
        "pimcore/object/gridcolumn/operator/WorkflowState.js",
        "pimcore/object/gridcolumn/value/DefaultValue.js",
        "pimcore/object/gridcolumn/operator/GeopointRenderer.js",
        "pimcore/object/gridcolumn/operator/ImageRenderer.js",
        "pimcore/object/gridcolumn/operator/HotspotimageRenderer.js",
        "pimcore/object/importcolumn/Abstract.js",
        "pimcore/object/importcolumn/operator/Base64.js",
        "pimcore/object/importcolumn/operator/Ignore.js",
        "pimcore/object/importcolumn/operator/Iterator.js",
        "pimcore/object/importcolumn/operator/LocaleSwitcher.js",
        "pimcore/object/importcolumn/operator/ObjectBrickSetter.js",
        "pimcore/object/importcolumn/operator/PHPCode.js",
        "pimcore/object/importcolumn/operator/Published.js",
        "pimcore/object/importcolumn/operator/Splitter.js",
        "pimcore/object/importcolumn/operator/Unserialize.js",
        "pimcore/object/importcolumn/value/DefaultValue.js",
        "pimcore/object/objectbrick.js",
        "pimcore/object/objectbricks/field.js",
        "pimcore/object/tags/abstract.js",
        "pimcore/object/tags/abstractRelations.js",
        "pimcore/object/tags/block.js",
        "pimcore/object/tags/rgbaColor.js",
        "pimcore/object/tags/date.js",
        "pimcore/object/tags/datetime.js",
        "pimcore/object/tags/time.js",
        "pimcore/object/tags/manyToOneRelation.js",
        "pimcore/object/tags/image.js",
        "pimcore/object/tags/encryptedField.js",
        "pimcore/object/tags/externalImage.js",
        "pimcore/object/tags/hotspotimage.js",
        "pimcore/object/tags/imagegallery.js",
        "pimcore/object/tags/video.js",
        "pimcore/object/tags/input.js",
        "pimcore/object/tags/classificationstore.js",
        "pimcore/object/tags/numeric.js",
        "pimcore/object/tags/manyToManyObjectRelation.js",
        "pimcore/object/tags/advancedManyToManyRelation.js",
        "pimcore/object/gridcolumn/operator/FieldCollectionGetter.js",
        "pimcore/object/tags/advancedManyToManyObjectRelation.js",
        "pimcore/object/tags/reverseObjectRelation.js",
        "pimcore/object/tags/urlSlug.js",
        "pimcore/object/tags/booleanSelect.js",
        "pimcore/object/tags/select.js",
        "pimcore/object/tags/user.js",
        "pimcore/object/tags/checkbox.js",
        "pimcore/object/tags/consent.js",
        "pimcore/object/tags/textarea.js",
        "pimcore/object/tags/wysiwyg.js",
        "pimcore/object/tags/slider.js",
        "pimcore/object/tags/manyToManyRelation.js",
        "pimcore/object/tags/table.js",
        "pimcore/object/tags/structuredTable.js",
        "pimcore/object/tags/country.js",
        "pimcore/object/tags/geo/abstract.js",
        "pimcore/object/tags/geobounds.js",
        "pimcore/object/tags/geopoint.js",
        "pimcore/object/tags/geopolygon.js",
        "pimcore/object/tags/geopolyline.js",
        "pimcore/object/tags/language.js",
        "pimcore/object/tags/password.js",
        "pimcore/object/tags/multiselect.js",
        "pimcore/object/tags/link.js",
        "pimcore/object/tags/fieldcollections.js",
        "pimcore/object/tags/localizedfields.js",
        "pimcore/object/tags/countrymultiselect.js",
        "pimcore/object/tags/languagemultiselect.js",
        "pimcore/object/tags/objectbricks.js",
        "pimcore/object/tags/firstname.js",
        "pimcore/object/tags/lastname.js",
        "pimcore/object/tags/email.js",
        "pimcore/object/tags/gender.js",
        "pimcore/object/tags/newsletterActive.js",
        "pimcore/object/tags/newsletterConfirmed.js",
        "pimcore/object/tags/targetGroup.js",
        "pimcore/object/tags/targetGroupMultiselect.js",
        "pimcore/object/tags/quantityValue.js",
        "pimcore/object/tags/inputQuantityValue.js",
        "pimcore/object/tags/calculatedValue.js",
        "pimcore/object/preview.js",
        "pimcore/object/versions.js",
        "pimcore/object/variantsTab.js",
        "pimcore/object/folder/search.js",
        "pimcore/object/edit.js",
        "pimcore/object/abstract.js",
        "pimcore/object/object.js",
        "pimcore/object/folder.js",
        "pimcore/object/variant.js",
        "pimcore/object/tree.js",
        "pimcore/object/layout/iframe.js",
        "pimcore/object/customviews/tree.js",
        "pimcore/object/quantityvalue/unitsettings.js",
        "pimcore/object/gridexport/csv.js",
        "pimcore/object/gridexport/xlsx.js",


        "pimcore/plugin/broker.js",
        "pimcore/plugin/plugin.js",


        "pimcore/report/panel.js",
        "pimcore/report/broker.js",
        "pimcore/report/abstract.js",
        "pimcore/report/settings.js",
        "pimcore/report/analytics/settings.js",
        "pimcore/report/analytics/elementoverview.js",
        "pimcore/report/analytics/elementexplorer.js",
        "pimcore/report/webmastertools/settings.js",
        "pimcore/report/tagmanager/settings.js",
        "pimcore/report/custom/item.js",
        "pimcore/report/custom/panel.js",
        "pimcore/report/custom/settings.js",
        "pimcore/report/custom/report.js",
        "pimcore/report/custom/definitions/sql.js",
        "pimcore/report/custom/definitions/analytics.js",
        "pimcore/report/custom/toolbarenricher.js",

        "pimcore/extensionmanager/admin.js",


        "pimcore/log/admin.js",
        "pimcore/log/detailwindow.js",


        "pimcore/layout/portal.js",
        "pimcore/layout/portlets/abstract.js",
        "pimcore/layout/portlets/modifiedDocuments.js",
        "pimcore/layout/portlets/modifiedObjects.js",
        "pimcore/layout/portlets/modifiedAssets.js",
        "pimcore/layout/portlets/modificationStatistic.js",
        "pimcore/layout/portlets/analytics.js",
        "pimcore/layout/portlets/customreports.js",

        "pimcore/layout/toolbar.js",
        "pimcore/layout/treepanelmanager.js",
        "pimcore/document/seemode.js",


        "pimcore/object/classificationstore/groupsPanel.js",
        "pimcore/object/classificationstore/propertiesPanel.js",
        "pimcore/object/classificationstore/collectionsPanel.js",
        "pimcore/object/classificationstore/keyDefinitionWindow.js",
        "pimcore/object/classificationstore/keySelectionWindow.js",
        "pimcore/object/classificationstore/relationSelectionWindow.js",
        "pimcore/object/classificationstore/storeConfiguration.js",
        "pimcore/object/classificationstore/storeTree.js",
        "pimcore/object/classificationstore/columnConfigDialog.js",


        "pimcore/workflow/transitionPanel.js",
        "pimcore/workflow/transitions.js",
        "pimcore/workflow/transitions.js",


        "pimcore/colorpicker-overrides.js",


        "pimcore/notification/helper.js",
        "pimcore/notification/panel.js",
        "pimcore/notification/modal.js"
    ];

    /**
     * @var string[]
     */
    protected $bundleScriptPaths;

    /**
     * @var string
     */
    protected $jsCacheDir;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @array
     */
    protected const WEBROOTPATHS = [
        PIMCORE_WEB_ROOT,
        PIMCORE_WEB_ROOT . '/bundles/pimcoreadmin/js/'
    ];

    /**
     * @string
     */
    protected const SCRIPT_INTERNAL = 'library';

    /**
     * @string
     */
    protected const SCRIPT_BUNDLE = 'bundle';

    /**
     * @string
     */
    protected const SCRIPT_PATH = '/bundles/pimcoreadmin/js/';

    /**
     * @param PimcoreBundleManager $pimcoreBundleManager
     * @param ContentSecurityPolicyHandler $contentSecurityPolicyHandler
     * @param LocaleService $localeService
     */
    public function __construct (
        private PimcoreBundleManager $pimcoreBundleManager,
        private ContentSecurityPolicyHandler $contentSecurityPolicyHandler,
        private LocaleService $localeService
    ) {
        $this->initLibScriptPaths();
        $this->initBundleScriptPaths();

        $this->filesystem = new Filesystem();
        $this->jsCacheDir = \Pimcore::getKernel()->getCacheDir() . '/minifiedJs/';
    }

    public function initLibScriptPaths()
    {
        // Add the suffix 'debug' to ext-all.js script path if the environment is not dev
        $debugSuffix = '';
        if (\Pimcore::disableMinifyJs()) {
            $debugSuffix = "-debug";
        }
        $this->libScriptPaths = str_replace ("../extjs/js/ext-all.js", "../extjs/js/ext-all$debugSuffix.js", $this->libScriptPaths);

        // Include the js file according to the locale
        $language = $this->localeService->getLocale();
        if (file_exists (PIMCORE_WEB_ROOT . '/bundles/pimcoreadmin/js/lib/ext-locale/locale-' . $language . '.js')) {
            array_push ($this->libScriptPaths, 'lib/ext-locale/locale-' . $language . '.js');
        }
    }

    public function initBundleScriptPaths()
    {
        $this->bundleScriptPaths = $this->pimcoreBundleManager->getJsPaths();
    }

    /**
     * @param bool $setDcVersion
     *
     * @return array
     */
    public function getLibScriptPaths(bool $setDcVersion = true): array
    {
        $setDcVersionText = ($setDcVersion) ? $this->getDcText() : '';

        return $this->getFormattedScripts($setDcVersionText . $this->getNonceText(), $this->libScriptPaths, self::SCRIPT_PATH);
    }

    /**
     * @param bool $setDcVersion
     *
     * @return array
     */
    public function getBundleScriptPaths(bool $setDcVersion = true): array
    {
        if (\Pimcore::disableMinifyJs()) {
            $setDcVersionText = ($setDcVersion) ? $this->getDcText('1') : '';

            return $this->getFormattedScripts($setDcVersionText . $this->getNoncetext(), $this->bundleScriptPaths);
        }

        return $this->getMinifiedScriptPaths(self::SCRIPT_BUNDLE, $this->bundleScriptPaths);
    }

    /**
     * @param bool $setDcVersion
     *
     * @return array
     */
    public function getInternalScriptPaths(bool $setDcVersion = true): array
    {
        if (\Pimcore::disableMinifyJs()) {
            $setDcVersionText = ($setDcVersion) ? $this->getDcText('1') : '';

            return $this->getFormattedScripts($setDcVersionText, $this->internalScriptPaths, self::SCRIPT_PATH);
        }

        return $this->getMinifiedScriptPaths(self::SCRIPT_INTERNAL, $this->internalScriptPaths);
    }

    /**
     * Get either pre-generated or runtime generated minified JS script paths
     *
     * @param string $prefix
     * @param array $scriptPaths
     *
     * @return array
     */
    public function getMinifiedScriptPaths(string $prefix, array $scriptPaths): array
    {
        $storageFile = $prefix . '_minified_javascript_core.js';

        if (!$this->isMinifiedScriptExists($storageFile)) {
            $storageFile = $this->minifyAndSaveJs($scriptPaths, $storageFile);
        }

        return [
            'storageFile' => basename ($storageFile),
            '_dc' => Version::getRevision()
        ];
    }

    /**
     * @param $storageFile
     *
     * returns false when script doesn't exist and path when exist
     *
     * @return bool|string
     */
    public function isMinifiedScriptExists($storageFile): bool|string
    {
        if ($this->filesystem->exists($this->jsCacheDir . $storageFile)) {
            return $this->jsCacheDir . $storageFile;
        }

        return false;
    }

    /**
     * Add prefixes like the path from root and postfixes like dc_version, nonce etc to the script paths
     *
     * @param string $postFixText
     * @param array $scripts
     * @param string $setPrefixText
     *
     * @return array
     */
    public function getFormattedScripts(string $postFixText, array $scripts, string $setPrefixText = ''): array
    {
        return array_map(function ($eachScriptPath) use ($setPrefixText, $postFixText) {
            return $setPrefixText . $eachScriptPath . $postFixText;
        }, $scripts);
    }

    /**
     * Minify all the script files passed to a single js script file
     *
     * @param array $jsScripts
     * @param string $storageFile
     *
     * @return string
     */
    protected function minifyAndSaveJs(array $jsScripts, string $storageFile): string
    {
        $scriptContents = '';
        foreach ($jsScripts as $scriptPath) {
            foreach (self::WEBROOTPATHS as $webRootPath) {
                $fullPath = $webRootPath . $scriptPath;
                if (file_exists ($fullPath)) {
                    $scriptContents .= file_get_contents ($fullPath) . "\n\n\n";
                }
            }
        }

        if ($this->writeToFile($this->jsCacheDir, $storageFile, $scriptContents)) {
            return $storageFile;
        }

        return '';
    }

    /**
     * @param string $dirPath
     * @param string $fileName
     * @param string $scriptContent
     *
     * @return bool
     */
    private function writeToFile(string $dirPath, string $fileName,string $scriptContent): bool
    {
        try {
            $fileName = $dirPath . $fileName;
            $this->filesystem->dumpFile($fileName, $scriptContent);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $dcVal
     *
     * @return string
     */
    protected function getDcText(string $dcVal = ''): string
    {
        if (!$dcVal) {
            $dcVal = Version::getRevision();
        }

        return '?_dc=' . $dcVal . '"';
    }

    /**
     * @return string
     */
    private function getNonceText(): string
    {
        return $this->contentSecurityPolicyHandler->getNonceHtmlAttribute();
    }

    /**
     * Warm up the js_cache folder on cache:warmup command
     *
     * @param string $cacheDir
     *
     * @return array|string[]
     */
    public function warmUp (string $cacheDir): array
    {
        if (!\Pimcore::disableMinifyJs ()) {
            $storagePaths = [];

            foreach ([
                         self::SCRIPT_INTERNAL . '_minified_javascript_core.js' => $this->internalScriptPaths,
                         self::SCRIPT_BUNDLE . '_minified_javascript_core.js' => $this->bundleScriptPaths
                     ] as $filename => $scripts) {
                $minifiedPaths = $this->minifyAndSaveJs($scripts, $filename);
                $storagePaths[] = $this->jsCacheDir . $minifiedPaths;
            }

            return $storagePaths;
        }

        return [];
    }
}


