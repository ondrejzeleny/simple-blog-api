<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Integration tests for authentication endpoints.
 */
class AuthenticationTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test successful user registration.
     */
    public function testUserRegistration(): void
    {
        // Arrange
        $userData = json_encode([
            'email' => 'newuser@test.com',
            'password' => 'StrongPassword123!',
            'name' => 'New User',
            'role' => 'reader',
        ]);

        // Act
        $this->client->request('POST', '/auth/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], (string) $userData);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        /** @var array{message: string} $response */
        $response = json_decode((string) $this->client->getResponse()->getContent(), true);
        $this->assertEquals('User registered successfully.', $response['message']);
    }

    /**
     * Test user registration with existing email fails.
     */
    public function testUserRegistrationWithExistingEmail(): void
    {
        // Arrange
        $userData = json_encode([
            'email' => 'admin@example.com', // Email from fixtures
            'password' => 'StrongPassword123!',
            'name' => 'Another User',
            'role' => 'reader',
        ]);

        // Act
        $this->client->request('POST', '/auth/register', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], (string) $userData);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
        /** @var array{error: string} $response */
        $response = json_decode((string) $this->client->getResponse()->getContent(), true);
        $this->assertEquals('User with this email already exists.', $response['error']);
    }

    /**
     * Test successful user login returns JWT token.
     */
    public function testUserLogin(): void
    {
        // Arrange
        $credentials = json_encode([
            'username' => 'admin@example.com',
            'password' => 'password1',
        ]);

        // Act
        $this->client->request('POST', '/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], (string) $credentials);

        // Assert
        $this->assertResponseIsSuccessful();
        /** @var array{token: string} $response */
        $response = json_decode((string) $this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $response);
        $this->assertNotEmpty($response['token']);
    }

    /**
     * Test login with invalid credentials fails.
     */
    public function testUserLoginWithInvalidCredentials(): void
    {
        // Arrange
        $credentials = json_encode([
            'username' => 'nonexistent@test.com',
            'password' => 'wrongpassword',
        ]);

        // Act
        $this->client->request('POST', '/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], (string) $credentials);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test protected endpoint without token returns 401.
     */
    public function testProtectedEndpointWithoutToken(): void
    {
        // Arrange + Act
        $this->client->request('GET', '/articles');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test protected endpoint with invalid token returns 401.
     */
    public function testProtectedEndpointWithInvalidToken(): void
    {
        // Arrange + Act
        $this->client->request('GET', '/articles', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer invalid_token',
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test protected endpoint with valid token succeeds.
     */
    public function testProtectedEndpointWithValidToken(): void
    {
        // Arrange
        $token = $this->getAuthToken('reader@example.com', 'password3');

        // Act
        $this->client->request('GET', '/articles', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
    }

    /**
     * Helper method to log in a user and get a JWT token.
     */
    private function getAuthToken(string $username, string $password): string
    {
        $this->client->request('POST', '/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], (string) json_encode([
            'username' => $username,
            'password' => $password,
        ]));

        $this->assertResponseIsSuccessful();
        $responseContent = (string) $this->client->getResponse()->getContent();
        /** @var array{token: string}|null $response */
        $response = json_decode($responseContent, true);

        $this->assertIsArray($response, 'Login response is not valid JSON.');
        $this->assertArrayHasKey('token', $response, 'Login response does not contain a token.');

        return $response['token'];
    }
}
