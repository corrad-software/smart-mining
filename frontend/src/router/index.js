import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  { path:'/login', component:()=>import('../pages/Login.vue'), meta:{guest:true} },
  { path:'/', redirect:'/map' },
  { path:'/dashboard', component:()=>import('../pages/Dashboard.vue') },
  { path:'/map', component:()=>import('../pages/MapPage.vue') },
  { path:'/live-view', component:()=>import('../pages/LiveView.vue') },
  { path:'/monitoring', component:()=>import('../pages/Monitoring.vue') },
  { path:'/reports', component:()=>import('../pages/Reports.vue') },
  { path:'/attendance', component:()=>import('../pages/Attendance.vue') },
  { path:'/site-management', component:()=>import('../pages/SiteManagement.vue') },
  { path:'/site/:id', component:()=>import('../pages/SiteDetails.vue') },
  { path:'/site/:id/f001-weekly-report/:weeklyId', component:()=>import('../pages/F001Report.vue') },
  { path:'/user-management', component:()=>import('../pages/UserManagement.vue') },
  { path:'/account', component:()=>import('../pages/Account.vue') },
]

const router = createRouter({ history: createWebHistory(), routes })
router.beforeEach((to)=>{
  const authed = localStorage.getItem('sm_auth')==='1'
  if(!to.meta.guest && !authed) return '/login'
  if(to.meta.guest && authed) return '/dashboard'
})
export default router
