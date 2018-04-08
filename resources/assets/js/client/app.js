

import './bootstrap';

import vSelect from 'vue-select'
Vue.component('v-select', vSelect);

import VueBarcode from '@xkeshi/vue-barcode';
Vue.component('barcode', VueBarcode);

Vue.component('top-nav', require('./components/top-nav'));
Vue.component('main-nav', require('./components/main-nav'));
Vue.component('sub-nav', require('./components/sub-nav'));
Vue.component('pager', require('./components/pager'));

Vue.component('home-view', require('./views/home'));
Vue.component('register-view', require('./views/auth/register'));
Vue.component('login-view', require('./views/auth/login'));

Vue.component('change-password-view', require('./views/users/change-password'));

Vue.component('notices-index', require('./views/notices/index'));
Vue.component('notice-details', require('./views/notices/details'));

Vue.component('centers-index', require('./views/centers/index'));

Vue.component('courses-index', require('./views/courses/index'));
Vue.component('courses-details', require('./views/courses/details'));

