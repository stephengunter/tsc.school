

import './bootstrap';

import vSelect from 'vue-select'
Vue.component('v-select', vSelect);

import VueBarcode from '@xkeshi/vue-barcode';
Vue.component('barcode', VueBarcode);

Vue.component('datetime-picker', require('./packages/components/DateTimePicker'));
Vue.component('alert', require('./packages/components/Alert'));
Vue.component('modal', require('./packages/components/Modal'));
Vue.component('toggle', require('./packages/components/Toggle'));
Vue.component('delete-confirm', require('./packages/components/DeleteConfirm'));
Vue.component('drop-down', require('./packages/components/DropDown'));
Vue.component('html-editor', require('./packages/components/HtmlEditor'));
Vue.component('check-box', require('./packages/components/Checkbox'));
Vue.component('check-box-list', require('./packages/components/CheckboxList'));

Vue.component('page-controll', require('./components/page-controll'));
Vue.component('admin-card', require('./components/admin-card'));
Vue.component('review-editor', require('./components/review-editor'));
Vue.component('ps-editor', require('./components/ps-editor'));
Vue.component('searcher', require('./components/searcher'));
Vue.component('user-selector', require('./components/user/selector'));

Vue.component('login-view', require('./views/login'));
Vue.component('change-password-view', require('./views/auth/change-password'));

Vue.component('home-view', require('./views/home'));

Vue.component('centers-index', require('./views/centers/index'));
Vue.component('centers-create', require('./views/centers/create'));
Vue.component('centers-details', require('./views/centers/details'));
Vue.component('centers-import', require('./views/centers/import'));

Vue.component('categories-index', require('./views/categories/index'));
Vue.component('categories-import', require('./views/categories/import'));

Vue.component('terms-index', require('./views/terms/index'));
Vue.component('terms-create', require('./views/terms/create'));
Vue.component('terms-details', require('./views/terms/details'));

Vue.component('teachers-index', require('./views/teachers/index'));
Vue.component('teachers-create', require('./views/teachers/create'));
Vue.component('teachers-details', require('./views/teachers/details'));
Vue.component('teachers-import', require('./views/teachers/import'));

Vue.component('volunteers-index', require('./views/volunteers/index'));
Vue.component('volunteers-create', require('./views/volunteers/create'));
Vue.component('volunteers-details', require('./views/volunteers/details'));
Vue.component('volunteers-import', require('./views/volunteers/import'));

Vue.component('admins-index', require('./views/admins/index'));
Vue.component('admins-create', require('./views/admins/create'));
Vue.component('admins-details', require('./views/admins/details'));
Vue.component('admins-import', require('./views/admins/import'));

Vue.component('courses-index', require('./views/courses/index'));
Vue.component('courses-details', require('./views/courses/details'));
Vue.component('courses-create', require('./views/courses/create'));
Vue.component('courses-import', require('./views/courses/import'));

Vue.component('users-index', require('./views/users/index'));
Vue.component('users-details', require('./views/users/details'));

Vue.component('signups-index', require('./views/signups/index'));
Vue.component('signups-report', require('./views/signups/report'));
Vue.component('signups-details', require('./views/signups/details'));
Vue.component('signups-create', require('./views/signups/create'));

Vue.component('quits-index', require('./views/quits/index'));
Vue.component('quits-details', require('./views/quits/details'));

Vue.component('students-index', require('./views/students/index'));
Vue.component('students-details', require('./views/students/details'));



import Footer from './components/footer'
new Vue({
  render: h => h(Footer)
}).$mount('#footer')
