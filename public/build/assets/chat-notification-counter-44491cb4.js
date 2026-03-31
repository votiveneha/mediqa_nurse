(function(){window.ChatNotificationCounter={userId:null,unreadCount:0,checkInterval:null,echo:null,init:function(t,e=null){this.userId=t,this.echo=e,console.log("🔔 Initializing Chat Notification Counter for user:",t),this.loadUnreadCount(),this.setupRealTimeListeners(),this.startPolling()},loadUnreadCount:function(){fetch("/nurse/chat/unread-count").then(t=>t.json()).then(t=>{t.unread_count!==void 0&&(this.updateCount(t.unread_count),console.log("📊 Unread count loaded:",t.unread_count))}).catch(t=>console.error("❌ Error loading unread count:",t))},setupRealTimeListeners:function(){if(!this.echo){console.warn("⚠️ Echo not available, using polling only");return}this.echo.private(`user.${this.userId}`).bind("new.message",e=>{console.log("📨 New message notification received:",e),this.incrementCount()}),console.log("✅ Real-time notification listener setup complete")},incrementCount:function(){this.unreadCount++,this.updateDisplay(),this.showNotification()},updateCount:function(t){this.unreadCount=parseInt(t)||0,this.updateDisplay()},resetCount:function(){this.unreadCount=0,this.updateDisplay()},updateDisplay:function(){const t=document.querySelector("#dropdownNotify");if(t){const e=t.querySelector(".notify-badge");if(e&&e.remove(),this.unreadCount>0){const n=document.createElement("span");if(n.className="notify-badge",n.textContent=this.unreadCount>99?"99+":this.unreadCount,n.style.cssText=`
                        position: absolute;
                        top: -5px;
                        right: -5px;
                        background: #dc3545;
                        color: white;
                        font-size: 10px;
                        font-weight: bold;
                        padding: 2px 6px;
                        border-radius: 10px;
                        min-width: 18px;
                        text-align: center;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                        animation: notifyPulse 2s infinite;
                    `,!document.getElementById("notify-pulse-style")){const o=document.createElement("style");o.id="notify-pulse-style",o.textContent=`
                            @keyframes notifyPulse {
                                0%, 100% { transform: scale(1); opacity: 1; }
                                50% { transform: scale(1.1); opacity: 0.8; }
                            }
                        `,document.head.appendChild(o)}t.style.position="relative",t.appendChild(n)}const i=document.querySelector('a.dropdown-item[href*="messages"]');i&&(i.textContent=`${this.unreadCount} message${this.unreadCount!==1?"s":""}`)}if(this.unreadCount>0){const e=document.title.replace(/\(\d+\)\s*/,"");document.title=`(${this.unreadCount}) ${e}`}},showNotification:function(){document.hidden&&"Notification"in window&&Notification.permission==="granted"&&new Notification("New Message",{body:"You have a new message",icon:"/nurse/assets/imgs/logo.png",badge:"/nurse/assets/imgs/logo.png"})},startPolling:function(){this.checkInterval=setInterval(()=>{this.loadUnreadCount()},3e4)},stopPolling:function(){this.checkInterval&&(clearInterval(this.checkInterval),this.checkInterval=null)}},document.addEventListener("DOMContentLoaded",function(){const t=document.querySelector("[data-user-id]"),e=t?t.dataset.userId:null;e?(window.ChatNotificationCounter.init(e,window.Echo||null),console.log("✅ ChatNotificationCounter initialized")):console.warn("⚠️ No user ID found, notification counter not initialized")})})();
