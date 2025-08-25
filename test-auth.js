// Test simple pour diagnostiquer le problème d'authentification

const BASE_URL = 'http://127.0.0.1:8000/api';

// Test 1: Route publique (classes)
async function testPublicRoute() {
    console.log('=== TEST ROUTE PUBLIQUE ===');
    try {
        const response = await fetch(`${BASE_URL}/classes`, {
            credentials: 'include'
        });
        console.log('Status:', response.status);
        console.log('Headers:', [...response.headers.entries()]);
        const data = await response.json();
        console.log('Data:', data);
    } catch (error) {
        console.error('Erreur route publique:', error);
    }
}

// Test 2: Login
async function testLogin() {
    console.log('\n=== TEST LOGIN ===');
    try {
        const response = await fetch(`${BASE_URL}/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify({
                email: 'admin@csndr.com',
                password: 'password123'
            })
        });
        console.log('Login Status:', response.status);
        console.log('Login Headers:', [...response.headers.entries()]);
        const data = await response.json();
        console.log('Login Data:', data);
        return response.ok;
    } catch (error) {
        console.error('Erreur login:', error);
        return false;
    }
}

// Test 3: Route protégée après login
async function testProtectedRoute() {
    console.log('\n=== TEST ROUTE PROTÉGÉE ===');
    try {
        const response = await fetch(`${BASE_URL}/users`, {
            credentials: 'include'
        });
        console.log('Protected Status:', response.status);
        console.log('Protected Headers:', [...response.headers.entries()]);
        if (response.ok) {
            const data = await response.json();
            console.log('Protected Data:', data);
        } else {
            const errorText = await response.text();
            console.log('Protected Error:', errorText);
        }
    } catch (error) {
        console.error('Erreur route protégée:', error);
    }
}

// Test 4: Vérification auth
async function testCheckAuth() {
    console.log('\n=== TEST CHECK AUTH ===');
    try {
        const response = await fetch(`${BASE_URL}/auth/check`, {
            credentials: 'include'
        });
        console.log('Check Auth Status:', response.status);
        const data = await response.json();
        console.log('Check Auth Data:', data);
    } catch (error) {
        console.error('Erreur check auth:', error);
    }
}

// Exécuter tous les tests
async function runAllTests() {
    await testPublicRoute();
    const loginSuccess = await testLogin();
    if (loginSuccess) {
        await testCheckAuth();
        await testProtectedRoute();
    }
}

runAllTests();
