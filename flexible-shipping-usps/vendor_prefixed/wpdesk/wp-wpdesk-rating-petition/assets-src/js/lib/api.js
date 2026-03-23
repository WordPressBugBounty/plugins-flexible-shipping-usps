export const postAjax = async (url, data) => {
  const body = new URLSearchParams(data);
  const res = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
    },
    body: body.toString(),
    credentials: 'same-origin',
  });
  const json = await res.json();
  if (!res.ok || !json) throw new Error(json?.data?.message || 'Request failed');
  return json;
};

export default postAjax;
