(() => {

'use strict';

/* CONFIG GLOBAL */
window.CONFIG = {
    siteUrl: window.location.origin,
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || ''
};

/* UTIL */
const $ = (s,scope=document)=>scope.querySelector(s);
const $$ = (s,scope=document)=>[...scope.querySelectorAll(s)];

/* LOADER */
window.loader={
    show(){ $('#spinnerOverlay')?.classList.add('active') },
    hide(){ $('#spinnerOverlay')?.classList.remove('active') }
};

/* SIDEBAR */
function initSidebar(){

const sidebar=$('#mainSidebar');
const content=$('#mainContent');
const toggle=$('#sidebarCollapse');
const overlay=$('#sidebarOverlay');

if(!sidebar||!toggle) return;

const mobile=()=>window.innerWidth<=768;

toggle.addEventListener('click',()=>{

if(mobile()){
sidebar.classList.toggle('mobile-open');
overlay?.classList.toggle('show');
}else{

const collapsed=sidebar.classList.toggle('collapsed');
content?.classList.toggle('expanded',collapsed);
localStorage.sidebarCollapsed=collapsed;

}

});

overlay?.addEventListener('click',()=>{
sidebar.classList.remove('mobile-open');
overlay.classList.remove('show');
});

}

/* TOOLTIPS */
function initTooltips(){
$$('[data-bs-toggle="tooltip"]').forEach(el=>{
new bootstrap.Tooltip(el)
});
}

/* SELECT2 */
function initSelect2(){
if(!window.jQuery||!$.fn.select2) return;
$('.select2').select2({theme:'bootstrap-5',width:'100%'});
}

/* DATEPICKER */
function initDatepickers(){
if(!$.fn.datepicker) return;

$('.datepicker').datepicker({
format:'dd/mm/yyyy',
language:'pt-BR',
autoclose:true,
todayHighlight:true
});
}

/* TIMEPICKER */
function initTimepickers(){
if(!$.fn.timepicker) return;
$('.timepicker').timepicker();
}

/* MASKS */
function initMasks(){
if($.fn.inputmask){
$('.mask-cpf').inputmask('999.999.999-99');
$('.mask-phone').inputmask('(99) 99999-9999');
$('.mask-date').inputmask('99/99/9999');
}
}

/* DATATABLES */
function initDataTables(){

if(typeof DataTable==='undefined') return;

DataTable.defaults={

...DataTable.defaults,

language:{url:'/assets/js/vendor/dataTables.pt-BR.json'},

responsive:true,
pageLength:25,

layout:{
topStart:'buttons',
topEnd:'search',
bottomStart:'info',
bottomEnd:'paging'
},

buttons:[
{extend:'excel',className:'btn btn-success btn-sm'},
{extend:'pdf',className:'btn btn-danger btn-sm'},
{extend:'print',className:'btn btn-secondary btn-sm'}
]

};

}

/* ALERT AUTO HIDE */
function initAlerts(){
setTimeout(()=>{
$$('.alert:not(.alert-permanent)').forEach(el=>{
el.style.opacity='0';
setTimeout(()=>el.remove(),500);
});
},5000);
}

/* NOTIFICATIONS */
function initNotificationCounter(){

setInterval(async()=>{

try{

const r=await fetch(CONFIG.siteUrl+'/admin/notifications/getUnreadCount');
const data=await r.json();

if(!data.success) return;

const btn=$('.notif-btn');
let dot=$('.notif-dot');

if(data.count>0){

if(!dot){
dot=document.createElement('span');
dot.className='notif-dot';
btn.appendChild(dot);
}

dot.textContent=data.count>9?'9+':data.count;

}else{
dot?.remove();
}

}catch(e){}

},60000);

}

/* TOAST */
window.toast={
success:(m)=>toastr.success(m),
error:(m)=>toastr.error(m),
warning:(m)=>toastr.warning(m),
info:(m)=>toastr.info(m)
};

/* SWEETALERT */
window.swal={

confirm:async(opts={})=>{

const res=await Swal.fire({
icon:'warning',
title:opts.title||'Tem certeza?',
text:opts.text||'Esta ação não pode ser desfeita',
showCancelButton:true
});

if(res.isConfirmed&&opts.callback) opts.callback();

}

};

/* API */
window.api={

async get(url){

loader.show();

try{

const r=await fetch(url,{
headers:{
'X-CSRF-TOKEN':CONFIG.csrfToken
}
});

return await r.json();

}finally{
loader.hide();
}

},

async post(url,data){

loader.show();

try{

const r=await fetch(url,{
method:'POST',
headers:{
'Content-Type':'application/json',
'X-CSRF-TOKEN':CONFIG.csrfToken
},
body:JSON.stringify(data)
});

return await r.json();

}finally{
loader.hide();
}

}

};

/* CRUD MODAL */
window.crud={

modal:null,

init(){
this.modal=new bootstrap.Modal('#crudModal');
},

async open(url){

loader.show();

try{

const r=await fetch(url);
const html=await r.text();

$('#crudModal .modal-content').innerHTML=html;

this.modal.show();

}finally{
loader.hide();
}

},

close(){
this.modal.hide();
}

};

/* INIT */
function init(){

initSidebar();
initTooltips();
initSelect2();
initDatepickers();
initTimepickers();
initMasks();
initDataTables();
initAlerts();
initNotificationCounter();

crud.init();

}

document.addEventListener('DOMContentLoaded',init);

})();