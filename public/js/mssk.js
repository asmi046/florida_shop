var element = document.querySelectorAll('input[type=tel]');
var maskOptions = {
    mask: '+{7}(000)000-00-00'
  };


for (let telElem of element)
    IMask(telElem, maskOptions);
