function downloadError(errorArr, errors) {
  for (let attr in errorArr) {
    errors.value[attr] = errorArr[attr][0];
  }
}
function clear(errors) {
  errors.value = {};
}

export { downloadError, clear };
