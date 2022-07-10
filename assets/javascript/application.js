import "core-js/es7";
import "regenerator-runtime/runtime";
import Main from '~/Main';

import { ActionPerformer } from './ActionPerformer';


async function getUsers () {
  const res = await fetch ('/app/api/test');
  const data = await res.json ();

  console.log (data);
}

getUsers ();

typeof uolkeo === 'object' && uolkeo.$$ready (function () {
  const htmlElementConfig = {
    '.form-textfield': Main.configTextField,
    '[data-dropdown]': Main.configDropDown,
    'select[data-list-select]': Main.configListSelect,
    'select[data-select]': Main.configAutoSelectField,
    '[data-tab]': Main.configTabControll
  }
  
  Object.keys (htmlElementConfig).map (function (queryStr) {
    document.querySelectorAll (queryStr).forEach (htmlElementConfig [queryStr]);
  });

  ActionPerformer.forEach (action => typeof action === 'function' && action ());
});
