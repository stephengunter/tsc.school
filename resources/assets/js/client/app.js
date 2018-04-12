

import './bootstrap';

import vSelect from 'vue-select'
Vue.component('v-select', vSelect);

Vue.component('datetime-picker', require('./packages/components/DateTimePicker'));


import VueBarcode from '@xkeshi/vue-barcode';
Vue.component('barcode', VueBarcode);

Vue.component('top-nav', require('./components/top-nav'));
Vue.component('main-nav', require('./components/main-nav'));
Vue.component('sub-nav', require('./components/sub-nav'));
Vue.component('pager', require('./components/pager'));

Vue.component('home-view', require('./views/home'));
Vue.component('register-view', require('./views/auth/register'));
Vue.component('login-view', require('./views/auth/login'));

Vue.component('forgot-password-view', require('./views/auth/forgot-password'));
Vue.component('change-password-view', require('./views/users/change-password'));
Vue.component('reset-password-view', require('./views/auth/reset-password'));

Vue.component('notices-index', require('./views/notices/index'));
Vue.component('notice-details', require('./views/notices/details'));

Vue.component('centers-index', require('./views/centers/index'));

Vue.component('courses-index', require('./views/courses/index'));
Vue.component('courses-details', require('./views/courses/details'));

Vue.component('user-profile-view', require('./views/users/profile'));

Vue.component('signup-index-view', require('./views/signups/index'));
Vue.component('signup-edit-view', require('./views/signups/edit'));

Vue.component('test', require('./views/test'));

