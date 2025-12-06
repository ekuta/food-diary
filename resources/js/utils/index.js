
// format: YYYYMMDD
export const getDate = (formattedDate) => {
//  const date = formattedDate.slice(0, 4) + '/' + formattedDate.substring(4, 6)  + '/' + formattedDate.slice(-2);
  return new Date(formattedDate);
//  return new Date(date + ' 00:00');
}

// Format is almost same as php except week(w) returns not number but week.
export const formatDate = (date = null, format = "Y-m-d") => {
  if (date == null) {
    date = new Date();
  }
  format = format.replace(/Y/g, date.getFullYear());
  format = format.replace(/m/g, ('0' + (date.getMonth() + 1)).slice(-2));
  format = format.replace(/n/g, date.getMonth() + 1);
  format = format.replace(/j/g, date.getDate());
  format = format.replace(/d/g, ('0' + date.getDate()).slice(-2));
  format = format.replace(/H/g, ('0' + date.getHours()).slice(-2));
  format = format.replace(/i/g, ('0' + date.getMinutes()).slice(-2));
  format = format.replace(/s/g, ('0' + date.getSeconds()).slice(-2));
  format = format.replace(/w/g, ['月', '火', '水', '木', '金', '土', '日'][date.getDay()]);
  return format;
}

export const round = (value, n = 0) => {
  const rounded = Math.round(value * (10 ** n)) / (10 ** n);
  if (rounded == value) {
    return String(value).replace(/((?<=\.\d+)|\.)0+$/, '');
  } else {
    return Math.round(value * (10 ** n)) / (10 ** n);
  }
}

export const getMealTypeName = (meal_type) => {
  const names = ['', '朝食', '昼食', '夕食', '間食'];
  return names[meal_type];
}
