import http from 'k6/http';
import { sleep, check } from 'k6';

const loginExpectedStatuses = http.expectedStatuses(200, 401, 403);

export const options = {
  stages: [
    { duration: '30s', target: 10 },
    { duration: '1m', target: 10 },
    { duration: '30s', target: 0 },
  ],
  thresholds: {
    http_req_duration: ['p(95)<500'],
    http_req_failed: ['rate<0.60'],
  },
};

export default function () {
  // Endpoint 1: Frontend principal
  const res1 = http.get('https://frontend-plantify.vercel.app');
  check(res1, {
    'homepage status 200': (r) => r.status === 200,
    'homepage < 500ms': (r) => r.timings.duration < 500,
  });
  sleep(1);

  // Endpoint 2: Login al backend real
  const res2 = http.post(
    'https://plantify.jamadev.com/index.php/login',
    JSON.stringify({
      correo: 'bryan@gmail.com',
      password: 'Hola.123',
    }),
    {
      headers: { 'Content-Type': 'application/json' },
      responseCallback: loginExpectedStatuses,
    }
  );
  check(res2, {
    'login respondió': (r) => r.status !== 0,
    'login no es error 500': (r) => r.status !== 500,
  });

  if (res2.status === 200) {
    const loginBody = res2.json();
    const userId = loginBody && loginBody.usuario ? loginBody.usuario.id : null;
    const sessionToken = loginBody && loginBody.usuario ? loginBody.usuario.session_token : null;

    if (userId && sessionToken) {
      const res3 = http.post(
        'https://plantify.jamadev.com/index.php/logout',
        JSON.stringify({
          id_usuario: userId,
          session_token: sessionToken,
        }),
        { headers: { 'Content-Type': 'application/json' } }
      );

      check(res3, {
        'logout status 200': (r) => r.status === 200,
      });
    }
  }

  sleep(1);
}
