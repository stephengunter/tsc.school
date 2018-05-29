
import Vue from 'vue';
window.Vue = Vue;

window._ = require('lodash');

try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

require('../../sass/client/_variables.scss');
import VueBlu from  'vue-blu';
Vue.use(VueBlu);


/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
   
}




import Helper from './helper';
window.Helper = Helper;
import Form from '../utilities/form';
window.Form = Form;

import Notice from './models/notice';
window.Notice=Notice;

import Course from './models/course';
window.Course=Course;

import User from './models/user';
window.User=User;

import Signup from './models/signup';
window.Signup=Signup;

import Bill from './models/bill';
window.Bill=Bill;

import Teacher from './models/teacher';
window.Teacher=Teacher;

import Student from './models/student';
window.Student=Student;

// import Menus from './models/menus';
// import Term from './models/term';
// import Teacher from './models/teacher';
// import TeacherGroup from './models/teacherGroup';
// import User from './models/user';
// import Admin from './models/admin';
// import Center from './models/center';
// import Category from './models/category';
// import ContactInfo from './models/contactInfo';
// import Course from './models/course';
// import ClassTime from './models/classTime';
// import Process from './models/process';
// import Signup from './models/signup';
// import Bill from './models/bill';
// import Student from './models/student';
// import Files from './models/files';

// window.Menus=Menus;
// window.Term=Term;
// window.Teacher=Teacher;
// window.TeacherGroup=TeacherGroup;
// window.User=User;
// window.Admin=Admin;
// window.Center=Center;
// window.Category=Category;
// window.ContactInfo=ContactInfo;
// window.Course=Course;
// window.ClassTime=ClassTime;
// window.Process=Process;
// window.Signup=Signup;
// window.Bill=Bill;
// window.Student=Student;
// window.Files=Files;


window.Bus = new Vue({});


Vue.filter('genderText', (gender)=> {
    if (Helper.isTrue(gender)) return '男';
    return '女';
});
Vue.filter('activeLabel', (active)=> {
    return Helper.activeLabel(active);

});
Vue.filter('reviewedLabel', (reviewed)=> {
    return Helper.reviewedLabel(reviewed);
});

Vue.filter('courseActiveLabel', (active)=> {
    return Course.activeLabel(active);
});

Vue.filter('formatMoney', (val)=> {
    return Helper.formatMoney(val);
});

Vue.filter('classTimesHtml', (course)=> {
    return Course.classTimesHtml(course)
});

Vue.filter('signupStatusLabel', (status)=> {
    return Signup.statusLabel(status)
});


