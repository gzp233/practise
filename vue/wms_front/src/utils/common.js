export function getXiaoqi(str, validity) {
    var y = Number(str.slice(0, 1));
    var j = Number(str.slice(1, 4)) - 1;
    var i = 1;
    var day = new Date(new Date().getFullYear(), i, 0).getDate();
    getM(day);
    function getM(day) {
      if (day >= j) {
      } else {
        i++;
        day = day + new Date(new Date().getFullYear(), i, 0).getDate();
        getM(day);
      }
    }
    var total = i + Number(validity);
    if (j == 0) total -= 1;
    if (y > 5) {
      y += 2010 + Math.floor(total / 12);
    } else {
      y += 2020 + Math.floor(total / 12);
    }
    total = total - Math.floor(total / 12) * 12;
    var result = "";
    if (total == 0) {
      result = y - 1 + "12";
    } else if (total < 10) {
      result = y + "0" + total;
    } else {
      result = y + "" + total;
    }
    return result;
  }

  export function createTime(){
    let ct = new Date();
    let y = ct.getFullYear();
    let m = ct.getMonth() + 1;
    let d = ct.getDate();
    let hours = ct.getHours();
    let minutes = ct.getMinutes();
    let seconds = ct.getSeconds();
    let sj = y +"-"+ m +"-" + d + " " + hours +":" + minutes +":"+ seconds;
    return sj;
  }