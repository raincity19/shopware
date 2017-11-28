import productList from 'module/core.product/src/components/page/core-product-list';
import productDetail from 'module/core.product/src/components/page/core-product-detail';
import productSidebar from 'module/core.product/src/components/organism/core-product-sidebar';
import 'module/core.product/src/components';

export default {
    id: 'core.product',
    name: 'Produkt Übersicht',
    description: 'Enter description here...',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#7AD5C8',
    icon: 'box',

    routes: {
        index: {
            components: {
                default: productList,
                sidebar: productSidebar
            },
            path: 'index'
        },

        create: {
            component: productDetail,
            path: 'product/create',
            meta: {
                parentPath: 'core.product.index'
            }
        },

        detail: {
            component: productDetail,
            path: 'detail/:uuid',
            meta: {
                parentPath: 'core.product.index'
            }
        }
    },

    navigation: {
        root: [{
            'core.product.index': {
                icon: 'box',
                color: '#7AD5C8',
                name: 'Produktübersicht'
            }
        }]
    },

    commands: [{
        title: 'Übersicht',
        route: 'product.index'
    }, {
        title: '%0 öffnen',
        route: 'product.detail'
    }],

    shortcuts: {
        index: {
            mac: {
                title: 'product.index.shortcut.mac',
                combination: [
                    'CMD',
                    'P'
                ]
            },
            win: {
                title: 'product.index.shortcut.win',
                combination: [
                    'CTRL',
                    'P'
                ]
            }
        }
    }
};