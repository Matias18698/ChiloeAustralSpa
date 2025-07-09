import { usePage } from "@inertiajs/vue3";

export function userData() {
  const { props } = usePage();

  function hasRol(rolsarray) {
    return props.auth.roles.some(element => rolsarray.includes(element));
  }

  function hasPermission(permissionsArray) {
    return props.auth.permissions.some(element => permissionsArray.includes(element));
  }

  return {
    hasRol,
    hasPermission,
  };
}