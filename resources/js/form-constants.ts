export const patterns = {
  username: /[0-9a-zA-Z]{4,20}$/,
  name: /[a-zA-Z]{4,20}$/,
  email: /^([\w.]{4,20})+@([\w-]+\.)+[\w-]{2,4}$/,
  birthday: /\d{4}-(0?[1-9]|1[012])-(0?[1-9]|[12][0-9]|3[01])$/,
  country: /^((?!default).)*$/,
};

export const errorMessages = {
  empty: '* Field should not be empty',
  short: '* Too short value',
  long: '* Too long value',
  wrong: '* Wrong value',
  required: '* This field is required',
  pattern: '* Field not valid',
};
