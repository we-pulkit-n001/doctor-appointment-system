let routes= [];

import dashboard from "./vue-routes-dashboard";
import patient from "./vue-routes-patients";
import doctor from "./vue-routes-doctors";
import appointment from "./vue-routes-appointments";

routes = routes.concat(dashboard);
routes = routes.concat(patient);
routes = routes.concat(doctor);
routes = routes.concat(appointment);

export default routes;
