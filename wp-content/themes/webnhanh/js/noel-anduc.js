(function(){
  const SNOW_ID   = 'anduc-snow';
  const TOGGLE_ID = 'anduc-noel-toggle';

  // Nhạc Noel – file jingle bells trên WebNhanh
  const AUDIO_SRC = 'https://webnhanh.net/wp-content/uploads/2025/12/jingle-bells.mp3';

  let audio = null;

  function createSnowflakes(container, count){
    const width = window.innerWidth;

    for(let i = 0; i < count; i++){
      const snowflake = document.createElement('div');
      snowflake.className = 'anduc-snowflake';
      snowflake.textContent = '❄';

      const startLeft = Math.random() * width;
      const duration = 8 + Math.random() * 8;
      const delay    = Math.random() * 8;
      const xMove    = (Math.random() - 0.5) * 200;

      snowflake.style.left = startLeft + 'px';
      snowflake.style.animationDuration = duration + 's';
      snowflake.style.animationDelay    = delay + 's';
      snowflake.style.setProperty('--anduc-x-move', xMove + 'px');
      snowflake.style.fontSize = (10 + Math.random() * 10) + 'px';

      container.appendChild(snowflake);
    }
  }

  function enableSnow(){
    const container = document.getElementById(SNOW_ID);
    if(!container) return;

    if (!container.dataset.inited) {
      let count = window.innerWidth < 768 ? 30 : 80;
      createSnowflakes(container, count);
      container.dataset.inited = '1';
    }

    container.style.display = 'block';
  }

  function disableSnow(){
    const container = document.getElementById(SNOW_ID);
    if(!container) return;
    container.style.display = 'none';
  }

  function initAudio(){
    if(audio) return audio;
    audio = new Audio(AUDIO_SRC);
    audio.loop   = true;
    audio.volume = 0.6;
    return audio;
  }

  function playAudio(){
    const a = initAudio();
    a.play().catch(function(e){
      console.warn('Audio play bị chặn:', e);
    });
  }

  function pauseAudio(){
    if(audio){
      audio.pause();
    }
  }

  function init(){
    const toggle = document.getElementById(TOGGLE_ID);
    const snow   = document.getElementById(SNOW_ID);
    if(!toggle || !snow) return;

    // Tuyết tự bật khi vào trang
    enableSnow();

    // CLICK cây thông mới bật/tắt nhạc + tuyết
    toggle.addEventListener('click', function(){
      const isOn = snow.style.display !== 'none';

      if(isOn){
        disableSnow();
        pauseAudio();
      } else {
        enableSnow();
        playAudio();
      }
    });

    window.addEventListener('resize', function(){
      if(snow.style.display !== 'none'){
        enableSnow();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', init);
})();
