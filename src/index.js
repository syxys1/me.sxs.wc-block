import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl, RangeControl, ColorPicker, TextControl } from '@wordpress/components';

registerBlockType('custom-product-collection/block', {
    title: __('Product Collection', 'custom-product-collection'),
    icon: 'cart',
    category: 'woocommerce',
    attributes: {
        excludeCategories: { type: 'array', default: [] },
        orderBy: { type: 'string', default: 'date' },
        order: { type: 'string', default: 'DESC' },
        columns: { type: 'number', default: 4 },
        title: { type: 'string', default: 'Product Collection' },
        showSubcategories: { type: 'boolean', default: true },
        titleFontSize: { type: 'number', default: 24 },
        titleFontColor: { type: 'string', default: '#333333' },
        separatorColor: { type: 'string', default: '#dddddd' },
        separatorThickness: { type: 'number', default: 1 },
        showPrice: { type: 'boolean', default: true },
        showAddToCart: { type: 'boolean', default: true },
        productFontSize: { type: 'number', default: 14 },
        productMargin: { type: 'number', default: 10 },
        productBorderColor: { type: 'string', default: '#dddddd' },
        productBorderStyle: { type: 'string', default: 'solid' },
        // New attributes for accordion customization
        accordionTitleFontSize: { type: 'number', default: 18 },
        accordionTitleFontColor: { type: 'string', default: '#333' },
        accordionCaretColor: { type: 'string', default: '#000' },
    },

    edit: ({ attributes, setAttributes }) => {
        const {
            excludeCategories, orderBy, order, columns, title, showSubcategories,
            titleFontSize, titleFontColor, separatorColor, separatorThickness,
            showPrice, showAddToCart, productFontSize, productMargin,
            productBorderColor, productBorderStyle,
            accordionTitleFontSize, accordionTitleFontColor, accordionCaretColor,
        } = attributes;

        const blockProps = useBlockProps();
        const allCategories = customProductCollectionData.categories || [];

        // Add category exclude toggle logic
        const addExcludedCategory = (slug) => {
            if (!excludeCategories.includes(slug)) {
                setAttributes({ excludeCategories: [...excludeCategories, slug] });
            }
        };

        const removeExcludedCategory = (slug) => {
            const updatedCategories = excludeCategories.filter((category) => category !== slug);
            setAttributes({ excludeCategories: updatedCategories });
        };

        return (
            <div {...blockProps}>
                <InspectorControls>
                    <PanelBody title={__('General Settings', 'custom-product-collection')}>
                        <TextControl
                            label={__('Block Title', 'custom-product-collection')}
                            value={title}
                            onChange={(newTitle) => setAttributes({ title: newTitle })}
                        />
                        <ToggleControl
                            label={__('Show Subcategory Titles', 'custom-product-collection')}
                            checked={showSubcategories}
                            onChange={(newShowSubcategories) => setAttributes({ showSubcategories: newShowSubcategories })}
                        />
                        <RangeControl
                            label={__('Columns', 'custom-product-collection')}
                            value={columns}
                            onChange={(newColumns) => setAttributes({ columns: newColumns })}
                            min={1}
                            max={6}
                        />
                        <SelectControl
                            label={__('Order By', 'custom-product-collection')}
                            value={orderBy}
                            options={[
                                { label: 'Date', value: 'date' },
                                { label: 'Title', value: 'title' },
                                { label: 'Price', value: 'meta_value' },
                                { label: 'Category', value: 'category' },
                            ]}
                            onChange={(newOrderBy) => setAttributes({ orderBy: newOrderBy })}
                        />
                        <SelectControl
                            label={__('Order', 'custom-product-collection')}
                            value={order}
                            options={[
                                { label: 'Ascending', value: 'ASC' },
                                { label: 'Descending', value: 'DESC' },
                            ]}
                            onChange={(newOrder) => setAttributes({ order: newOrder })}
                        />
                    </PanelBody>
                    
                    <PanelBody title={__('Accordion Settings', 'custom-product-collection')}>
                        <RangeControl
                            label={__('Title Font Size', 'custom-product-collection')}
                            value={accordionTitleFontSize}
                            onChange={(newSize) => setAttributes({ accordionTitleFontSize: newSize })}
                            min={14}
                            max={30}
                        />
                        <ColorPicker
                            label={__('Title Font Color', 'custom-product-collection')}
                            value={accordionTitleFontColor}
                            onChange={(newColor) => setAttributes({ accordionTitleFontColor: newColor })}
                        />
                        <ColorPicker
                            label={__('Caret Color', 'custom-product-collection')}
                            value={accordionCaretColor}
                            onChange={(newColor) => setAttributes({ accordionCaretColor: newColor })}
                        />
                    </PanelBody>

                    <PanelBody title={__('Category Settings', 'custom-product-collection')} initialOpen={false}>
                        <RangeControl
                            label={__('Category Title Font Size', 'custom-product-collection')}
                            value={titleFontSize}
                            onChange={(newFontSize) => setAttributes({ titleFontSize: newFontSize })}
                            min={16}
                            max={36}
                        />
                        <ColorPicker
                            label={__('Category Title Font Color', 'custom-product-collection')}
                            value={titleFontColor}
                            onChange={(newColor) => setAttributes({ titleFontColor: newColor })}
                        />
                        <RangeControl
                            label={__('Separator Thickness', 'custom-product-collection')}
                            value={separatorThickness}
                            onChange={(newThickness) => setAttributes({ separatorThickness: newThickness })}
                            min={1}
                            max={10}
                        />
                        <ColorPicker
                            label={__('Separator Color', 'custom-product-collection')}
                            value={separatorColor}
                            onChange={(newColor) => setAttributes({ separatorColor: newColor })}
                        />
                    </PanelBody>

                    <PanelBody title={__('Product Settings', 'custom-product-collection')} initialOpen={false}>
                        <RangeControl
                            label={__('Product Font Size', 'custom-product-collection')}
                            value={productFontSize}
                            onChange={(newFontSize) => setAttributes({ productFontSize: newFontSize })}
                            min={12}
                            max={18}
                        />
                        <RangeControl
                            label={__('Product Margin', 'custom-product-collection')}
                            value={productMargin}
                            onChange={(newMargin) => setAttributes({ productMargin: newMargin })}
                            min={0}
                            max={20}
                        />
                        <ColorPicker
                            label={__('Product Border Color', 'custom-product-collection')}
                            value={productBorderColor}
                            onChange={(newColor) => setAttributes({ productBorderColor: newColor })}
                        />
                        <SelectControl
                            label={__('Product Border Style', 'custom-product-collection')}
                            value={productBorderStyle}
                            options={[
                                { label: 'Solid', value: 'solid' },
                                { label: 'Dashed', value: 'dashed' },
                                { label: 'Dotted', value: 'dotted' },
                            ]}
                            onChange={(newBorderStyle) => setAttributes({ productBorderStyle: newBorderStyle })}
                        />
                        <ToggleControl
                            label={__('Show Price', 'custom-product-collection')}
                            checked={showPrice}
                            onChange={(newShowPrice) => setAttributes({ showPrice: newShowPrice })}
                        />
                        <ToggleControl
                            label={__('Show Add to Cart Button', 'custom-product-collection')}
                            checked={showAddToCart}
                            onChange={(newShowAddToCart) => setAttributes({ showAddToCart: newShowAddToCart })}
                        />
                    </PanelBody>
                </InspectorControls>
                <h2>{title}</h2>
                <p>{__('Customize the product collection settings in the sidebar.', 'custom-product-collection')}</p>
            </div>
        );
    },
    save: () => null, // Server-side rendering
});