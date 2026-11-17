const lightbox=document.querySelector('#photo-lightbox');
fetch('api/site-media.php',{cache:'no-store'}).then(response=>response.json()).then(data=>{const cover=data.slots?.find(slot=>slot.slot_key==='offers_cover');if(cover?.path)document.querySelector('.offers-hero').style.backgroundImage=`linear-gradient(90deg,rgba(5,9,22,.94),rgba(5,9,22,.35)),url("${cover.path}")`}).catch(()=>{});
const menuToggle=document.querySelector('.mobile-menu-toggle');if(menuToggle){menuToggle.addEventListener('click',()=>{const header=document.querySelector('.site-header'),open=header.classList.toggle('menu-open');menuToggle.setAttribute('aria-expanded',String(open));menuToggle.textContent=open?'×':'☰'})}
const closeLightbox=()=>{lightbox.classList.remove('open');lightbox.setAttribute('aria-hidden','true')};
document.querySelectorAll('.offer-photo [data-photo]').forEach(button=>{const title=button.dataset.title;const link=document.createElement('a');link.className='offer-whatsapp';link.href=`https://wa.me/56986771343?text=${encodeURIComponent(`Hola Ferretería El Volcán, quisiera solicitar información, precio y disponibilidad de esta oferta: ${title}.`)}`;link.target='_blank';link.rel='noopener';link.setAttribute('aria-label',`Solicitar por WhatsApp la oferta ${title}`);link.innerHTML='<span aria-hidden="true">●</span> Solicitar por WhatsApp ↗';button.closest('.offer-photo').appendChild(link)});
document.addEventListener('click',event=>{const trigger=event.target.closest('[data-photo]');if(trigger){const title=trigger.dataset.title;document.querySelector('#lightbox-image').src=`ferreteriaelvolcan/${trigger.dataset.photo}`;document.querySelector('#lightbox-image').alt=title;document.querySelector('#lightbox-title').textContent=title;document.querySelector('#lightbox-whatsapp').href=`https://wa.me/56986771343?text=${encodeURIComponent(`Hola Ferretería El Volcán, quisiera consultar disponibilidad y oferta vigente de: ${title}.`)}`;lightbox.classList.add('open');lightbox.setAttribute('aria-hidden','false');document.querySelector('.lightbox-close').focus()}if(event.target.closest('[data-close-lightbox]'))closeLightbox()});
document.addEventListener('keydown',event=>{if(event.key==='Escape')closeLightbox()});
document.querySelector('#year').textContent=new Date().getFullYear();

const adminMode=new URLSearchParams(location.search).get('edit')==='media';
if(adminMode){
  fetch('api/admin-session.php',{cache:'no-store'}).then(r=>r.json()).then(session=>{
    if(!session.authenticated)return;
    document.body.classList.add('media-editing');
    const hero=document.querySelector('.offers-hero');
    if(!hero)return;
    const button=document.createElement('button');
    button.type='button';button.className='site-media-edit-button';button.textContent='✎ Cambiar portada';
    const input=document.createElement('input');input.type='file';input.accept='image/jpeg,image/png,image/webp';input.hidden=true;
    hero.append(button,input);
    button.addEventListener('click',()=>input.click());
    input.addEventListener('change',()=>{
      const file=input.files[0];if(!file)return;
      const form=new FormData();form.append('csrf',session.csrf);form.append('slot_key','offers_cover');form.append('media',file);
      button.disabled=true;button.textContent='Subiendo…';
      fetch('api/admin-site-media.php',{method:'POST',body:form}).then(async response=>{const data=await response.json();if(!response.ok||!data.ok)throw new Error(data.error||'No se pudo actualizar la portada');hero.style.backgroundImage=`linear-gradient(90deg,rgba(5,9,22,.94),rgba(5,9,22,.35)),url("${data.path}")`;button.textContent='✓ Portada actualizada'}).catch(error=>{button.textContent='✎ Cambiar portada';alert(error.message)}).finally(()=>{button.disabled=false;input.value=''});
    });
  }).catch(()=>{});
}
