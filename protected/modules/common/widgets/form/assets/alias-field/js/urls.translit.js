/**
 * ГОСТ 7.79-2000
 * @see http://textpattern.ru/html/transliteration-tables.htm
 */
function cyr2lat(str) {
   
      var cyr2latChars = new Array(
  ['а', 'a'], ['б', 'b'], ['в', 'v'], ['г', 'g'],
  ['д', 'd'],  ['е', 'e'], ['ё', 'yo'], ['ж', 'zh'], ['з', 'z'],
  ['и', 'i'], ['й', 'j'], ['к', 'k'], ['л', 'l'],
  ['м', 'm'],  ['н', 'n'], ['о', 'o'], ['п', 'p'],  ['р', 'r'],
  ['с', 's'], ['т', 't'], ['у', 'u'], ['ф', 'f'],
  ['х', 'x'],  ['ц', 'c'], ['ч', 'ch'],['ш', 'sh'], ['щ', 'shh'],
  ['ъ', ''],  ['ы', 'y'], ['ь', ''],  ['э', 'e'], ['ю', 'yu'], ['я', 'ya'],
   
  ['А', 'A'], ['Б', 'B'],  ['В', 'V'], ['Г', 'G'],
  ['Д', 'D'], ['Е', 'E'], ['Ё', 'YO'],  ['Ж', 'ZH'], ['З', 'Z'],
  ['И', 'I'], ['Й', 'J'],  ['К', 'K'], ['Л', 'L'],
  ['М', 'M'], ['Н', 'N'], ['О', 'O'],  ['П', 'P'],  ['Р', 'R'],
  ['С', 'S'], ['Т', 'T'],  ['У', 'U'], ['Ф', 'F'],
  ['Х', 'X'], ['Ц', 'C'], ['Ч', 'CH'], ['Ш', 'SH'], ['Щ', 'SHH'],
  ['Ъ', ''],  ['Ы', 'Y'],
  ['Ь', ''],
  ['Э', 'E'],
  ['Ю', 'YU'],
  ['Я', 'YA'],
   
  ['a', 'a'], ['b', 'b'], ['c', 'c'], ['d', 'd'], ['e', 'e'],
  ['f', 'f'], ['g', 'g'], ['h', 'h'], ['i', 'i'], ['j', 'j'],
  ['k', 'k'], ['l', 'l'], ['m', 'm'], ['n', 'n'], ['o', 'o'],
  ['p', 'p'], ['q', 'q'], ['r', 'r'], ['s', 's'], ['t', 't'],
  ['u', 'u'], ['v', 'v'], ['w', 'w'], ['x', 'x'], ['y', 'y'],
  ['z', 'z'],
   
  ['A', 'A'], ['B', 'B'], ['C', 'C'], ['D', 'D'],['E', 'E'],
  ['F', 'F'],['G', 'G'],['H', 'H'],['I', 'I'],['J', 'J'],['K', 'K'],
  ['L', 'L'], ['M', 'M'], ['N', 'N'], ['O', 'O'],['P', 'P'],
  ['Q', 'Q'],['R', 'R'],['S', 'S'],['T', 'T'],['U', 'U'],['V', 'V'],
  ['W', 'W'], ['X', 'X'], ['Y', 'Y'], ['Z', 'Z'],
   
  [' ', '-'],['0', '0'],['1', '1'],['2', '2'],['3', '3'],
  ['4', '4'],['5', '5'],['6', '6'],['7', '7'],['8', '8'],['9', '9'],
  ['-', '-']
   
      );
   
      var newStr = new String();
   
      for (var i = 0; i < str.length; i++) {
   
          ch = str.charAt(i);
          var newCh = '';
   
          for (var j = 0; j < cyr2latChars.length; j++) {
              if (ch == cyr2latChars[j][0]) {
                  newCh = cyr2latChars[j][1];
   
              }
          }
          // Если найдено совпадение, то добавляется соответствие, если нет - пустая строка
          newStr += newCh;
   
      }
      // Удаляем повторяющие знаки - Именно на них заменяются пробелы.
      // Так же удаляем символы перевода строки, но это наверное уже лишнее
      var newStr = newStr.replace(/[-]{2,}/gim, '-').replace(/\n/gim, '');
      return newStr.toLowerCase();
  }
