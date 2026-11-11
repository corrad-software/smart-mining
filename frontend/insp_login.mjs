import { chromium } from 'playwright'
const b=await chromium.launch();const p=await b.newPage({viewport:{width:1440,height:900}})
await p.goto('https://smart-mining.selgem.com/login',{waitUntil:'domcontentloaded',timeout:60000})
await p.waitForTimeout(4000)
const info=await p.evaluate(()=>{
  const out={imgs:[],videos:[],bgImages:[],bigText:[]}
  document.querySelectorAll('img').forEach(i=>out.imgs.push({src:i.src.split('/').pop(), w:i.naturalWidth, h:i.naturalHeight}))
  document.querySelectorAll('video,source').forEach(v=>out.videos.push(v.src||v.currentSrc))
  document.querySelectorAll('*').forEach(e=>{const bg=getComputedStyle(e).backgroundImage; if(bg&&bg!=='none'&&bg.includes('url')) out.bgImages.push(bg.slice(0,90))})
  // big headline text (the yellow title / stats)
  document.querySelectorAll('h1,h2,h3,div,span').forEach(e=>{const t=(e.innerText||'').trim(); const fs=parseFloat(getComputedStyle(e).fontSize); if(t && t.length<60 && fs>22 && e.children.length===0) out.bigText.push(t)})
  out.bgImages=[...new Set(out.bgImages)]; out.bigText=[...new Set(out.bigText)].slice(0,20)
  return out
})
console.log(JSON.stringify(info,null,1))
await b.close()
