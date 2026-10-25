// 简化的stadium.js文件，用于解决引用错误
console.log('stadium.js loaded successfully');

// 基础对象定义
window.stadium = {
  init: function() {
    console.log('stadium initialized');
  },
  
  getVersion: function() {
    return '1.0.0';
  }
};

// 自动初始化
if (typeof document !== 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      window.stadium.init();
    });
  } else {
    window.stadium.init();
  }
}