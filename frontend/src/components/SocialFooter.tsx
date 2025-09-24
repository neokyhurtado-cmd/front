import React, { useEffect } from 'react'

export default function SocialFooter(){
  useEffect(()=>{
    function adjust(){
      try{
        var f = document.getElementById('site-footer') as HTMLElement | null;
        if (!f) return;
        var h = f.offsetHeight || 80;
        document.body.style.paddingBottom = (h + 12) + 'px';
      }catch(e){console.error(e)}
    }
    window.addEventListener('load', adjust)
    window.addEventListener('resize', adjust)
    setTimeout(adjust,200)
    return ()=>{
      window.removeEventListener('load', adjust)
      window.removeEventListener('resize', adjust)
    }
  }, [])

  return (
  <div id="site-footer" className="site-footer" style={{padding:'30px 20px',background:'#f8f9fb',textAlign:'center',position:'fixed',left:0,right:0,bottom:0,zIndex:1000}}>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-p1Cm2lX2Y6V+3s4G6eJp2KfQqY1+W0e7dQb0Z7Xlqg6JtWlYFQ1D9z2k5k6w3m8c1b4Iv7Y4hZ2q3y6vF9KNg==" crossOrigin="anonymous" referrerPolicy="no-referrer" />
      <style>{`.social-list{list-style:none;padding:0;margin:0 0 10px;display:flex;justify-content:center;gap:18px}.social-list li{display:inline-flex}.social-list a{display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:50%;background:#fff;color:#333;border:1px solid #e6e9ef;text-decoration:none;box-shadow:0 2px 6px rgba(22,28,37,0.06)}.social-list a .label{display:none}.social-list a i{font-size:18px}.footer-info{margin-top:14px;color:#4b5563;font-size:14px}@media (max-width:480px){.social-list{gap:12px}.social-list a{width:40px;height:40px}}`}</style>

      <ul className="social-list" aria-label="Redes sociales Panorama Ingenieria">
        <li><a href="#" className="social-facebook" title="Facebook"><img src="/img/icons/facebook.svg" alt="Facebook" width="28" height="28" /></a></li>
        <li><a href="#" className="social-linkedin" title="LinkedIn"><img src="/img/icons/linkedin.svg" alt="LinkedIn" width="28" height="28" /></a></li>
        <li><a href="#" className="social-instagram" title="Instagram"><img src="/img/icons/instagram.svg" alt="Instagram" width="28" height="28" /></a></li>
        <li><a href="#" className="social-google" title="Google"><img src="/img/icons/google.svg" alt="Google" width="28" height="28" /></a></li>
      </ul>

      <div className="footer-info">
        <div className="company-name">Panorama Ingenieria</div>
        <div className="company-details">Dirección · Teléfono · Correo · Horarios</div>
      </div>
    </div>
  )
}
