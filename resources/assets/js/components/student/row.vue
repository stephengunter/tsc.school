<template>
    <tr>
        <td v-if="can_check">
            <check-box :value="student.id" :default="beenChecked(student.id)"
                @selected="onChecked" @unselected="unChecked">
            </check-box>
        </td>
        <td>
            <a href="#" @click.prevent="onSelected(student.id)" v-text="student.user.profile.fullname"> </a> 
        </td>
        <td>{{  getScore(student) }}</td>
        <td>{{  student.user.email }}</td>
        <td>{{  student.user.phone }}</td>
        <td>
            <span v-if="student.status < 0" class="label label-default"> 已退出 </span>
        </td>
        
        
        
    </tr> 
</template>

<script>
export default {
    name:'StudentRow',
    props: {
        student: {
            type: Object,
            default: null
        },
        can_check: {
            type: Boolean,
            default: false
        },
        editting_score: {
            type: Boolean,
            default: false
        },
	},
}
</script>

