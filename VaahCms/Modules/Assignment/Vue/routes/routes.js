let routes= [];

import dashboard from "./vue-routes-dashboard";
import patient from "./vue-routes-patients";

routes = routes.concat(dashboard);
routes = routes.concat(patient);

export default routes;
