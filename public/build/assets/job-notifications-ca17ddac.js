class r{constructor(){this.notificationContainer=null,this.notifications=[],this.maxNotifications=10,this.soundEnabled=!0,this.init()}init(){this.createNotificationContainer(),this.listenForJobPublished(),this.setupNotificationClickHandler(),this.loadExistingNotifications()}createNotificationContainer(){let i=document.getElementById("jobNotificationsContainer");i||(i=document.createElement("div"),i.id="jobNotificationsContainer",i.style.cssText=`
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
            `,document.body.appendChild(i)),this.notificationContainer=i}listenForJobPublished(){var t;if(typeof window.Echo>"u"){console.error("Laravel Echo not found. Make sure it is loaded before this script.");return}const i=(t=window.Laravel)==null?void 0:t.userId;if(!i){console.error("User ID not found in window.Laravel");return}console.log("Subscribing to job notifications for user:",i),window.Echo.private(`user.${i}`).listen(".job.published",e=>{console.log("New job published event received:",e),this.showJobNotification(e),this.addNotificationToList(e),this.playNotificationSound(),this.updateNotificationBadge()})}showJobNotification(i){const t=document.createElement("div");t.className="job-notification-toast",t.dataset.jobId=i.job_id,t.style.cssText=`
            background: white;
            border-left: 4px solid #28a745;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            animation: slideInRight 0.3s ease-out;
        `,t.innerHTML=`
            <div style="display: flex; align-items: start; gap: 12px;">
                <div style="flex-shrink: 0; width: 40px; height: 40px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-briefcase" style="color: white; font-size: 18px;"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: #333; margin-bottom: 4px;">New Job Posted!</div>
                    <div style="font-size: 14px; color: #666; margin-bottom: 4px;">${this.escapeHtml(i.title)}</div>
                    <div style="font-size: 12px; color: #999;">
                        <i class="fas fa-hospital"></i> ${this.escapeHtml(i.facility_name)}<br>
                        <i class="fas fa-map-marker-alt"></i> ${this.escapeHtml(i.location)}
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; cursor: pointer; color: #999; font-size: 20px; padding: 0; line-height: 1;">
                    &times;
                </button>
            </div>
        `,t.addEventListener("click",e=>{e.target.tagName!=="BUTTON"&&this.viewJobDetails(i.job_id)}),this.notificationContainer.insertBefore(t,this.notificationContainer.firstChild),setTimeout(()=>{t.style.animation="slideOutRight 0.3s ease-out",setTimeout(()=>t.remove(),300)},8e3)}addNotificationToList(i){this.notifications.unshift({job_id:i.job_id,title:i.title,facility_name:i.facility_name,location:i.location,specialty:i.specialty,message:i.message,created_at:i.created_at,read:!1}),this.notifications.length>this.maxNotifications&&(this.notifications=this.notifications.slice(0,this.maxNotifications)),this.saveNotifications()}loadExistingNotifications(){const i=localStorage.getItem("jobNotifications");if(i)try{this.notifications=JSON.parse(i),this.updateNotificationBadge()}catch(t){console.error("Error loading notifications:",t)}}saveNotifications(){localStorage.setItem("jobNotifications",JSON.stringify(this.notifications))}updateNotificationBadge(){const i=this.notifications.filter(e=>!e.read).length;let t=document.getElementById("jobNotificationBadge");!t&&i>0&&(t=document.createElement("span"),t.id="jobNotificationBadge",t.style.cssText=`
                position: fixed;
                top: 10px;
                right: 10px;
                background: #dc3545;
                color: white;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: bold;
                z-index: 10000;
                cursor: pointer;
            `,document.body.appendChild(t),t.addEventListener("click",()=>{this.showNotificationPanel()})),t&&(i>0?(t.textContent=i>9?"9+":i,t.style.display="flex"):t.style.display="none")}markAsRead(i){const t=this.notifications.find(e=>e.job_id==i);t&&(t.read=!0,this.saveNotifications(),this.updateNotificationBadge())}viewJobDetails(i){var e;this.markAsRead(i);const t=((e=window.Laravel)==null?void 0:e.baseUrl)+"/nurse/jobs/"+i;window.open(t,"_blank")}showNotificationPanel(){let i=document.getElementById("jobNotificationPanel");i||(i=document.createElement("div"),i.id="jobNotificationPanel",i.style.cssText=`
                position: fixed;
                top: 60px;
                right: 20px;
                width: 400px;
                max-height: 600px;
                background: white;
                box-shadow: 0 4px 20px rgba(0,0,0,0.2);
                border-radius: 12px;
                z-index: 10000;
                overflow: hidden;
                display: none;
            `,i.innerHTML=`
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; font-weight: 600; font-size: 18px; display: flex; justify-content: space-between; align-items: center;">
                    <span><i class="fas fa-bell"></i> Job Notifications</span>
                    <button id="closeNotificationPanel" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
                <div id="notificationList" style="max-height: 500px; overflow-y: auto;"></div>
                <div style="padding: 12px; text-align: center; border-top: 1px solid #eee;">
                    <button id="markAllAsRead" style="background: #28a745; color: white; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer;">Mark All as Read</button>
                </div>
            `,document.body.appendChild(i),document.getElementById("closeNotificationPanel").addEventListener("click",()=>{i.style.display="none"}),document.getElementById("markAllAsRead").addEventListener("click",()=>{this.markAllAsRead()}));const t=document.getElementById("notificationList");this.notifications.length===0?t.innerHTML='<div style="padding: 40px; text-align: center; color: #999;"><i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 12px;"></i><p>No notifications yet</p></div>':(t.innerHTML=this.notifications.map(e=>`
                <div class="notification-item" data-job-id="${e.job_id}" style="padding: 16px; border-bottom: 1px solid #eee; cursor: pointer; ${e.read?"":"background: #f8f9fa;"}">
                    <div style="display: flex; gap: 12px; align-items: start;">
                        <div style="flex-shrink: 0; width: 36px; height: 36px; background: ${e.read?"#e9ecef":"#28a745"}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-briefcase" style="color: ${e.read?"#999":"white"}; font-size: 16px;"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: ${e.read?"400":"600"}; color: #333; margin-bottom: 4px;">${this.escapeHtml(e.title)}</div>
                            <div style="font-size: 13px; color: #666;">
                                <i class="fas fa-hospital"></i> ${this.escapeHtml(e.facility_name)} | 
                                <i class="fas fa-map-marker-alt"></i> ${this.escapeHtml(e.location)}
                            </div>
                            <div style="font-size: 11px; color: #999; margin-top: 4px;">${this.formatTime(e.created_at)}</div>
                        </div>
                        ${e.read?"":'<div style="width: 10px; height: 10px; background: #28a745; border-radius: 50%; flex-shrink: 0;"></div>'}
                    </div>
                </div>
            `).join(""),t.querySelectorAll(".notification-item").forEach(e=>{e.addEventListener("click",()=>{const o=e.dataset.jobId;this.viewJobDetails(o)})})),i.style.display="block"}markAllAsRead(){this.notifications.forEach(i=>i.read=!0),this.saveNotifications(),this.updateNotificationBadge(),this.showNotificationPanel()}playNotificationSound(){if(!(!this.soundEnabled||!document.hidden))try{new Audio("/sounds/notification.mp3").play().catch(()=>{})}catch{}}setupNotificationClickHandler(){}escapeHtml(i){if(!i)return"";const t=document.createElement("div");return t.textContent=i,t.innerHTML}formatTime(i){const t=new Date(i),o=new Date-t,a=Math.floor(o/6e4),s=Math.floor(o/36e5),d=Math.floor(o/864e5);return a<1?"Just now":a<60?`${a}m ago`:s<24?`${s}h ago`:d<7?`${d}d ago`:t.toLocaleDateString()}}document.addEventListener("DOMContentLoaded",()=>{var n,i;(((n=window.Laravel)==null?void 0:n.userRole)==="nurse"||(i=window.Laravel)!=null&&i.isNurse)&&(console.log("Initializing Job Notification Manager..."),window.jobNotificationManager=new r)});const l=document.createElement("style");l.textContent=`
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .notification-item:hover {
        background: #f0f0f0 !important;
    }
`;document.head.appendChild(l);window.JobNotificationManager=r;
