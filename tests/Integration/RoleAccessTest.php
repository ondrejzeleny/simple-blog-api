<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Integration tests for role-based access control.
 */
class RoleAccessTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test reader can view articles.
     */
    public function testReaderCanViewArticles(): void
    {
        // Arrange
        $token = $this->getAuthToken('reader@example.com', 'password3');

        // Act
        $this->client->request('GET', '/articles', [], [], ['HTTP_AUTHORIZATION' => 'Bearer '.$token]);

        // Assert
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test reader cannot create, edit or delete articles.
     */
    public function testReaderCannotCreateOrEditOrDeleteArticle(): void
    {
        // Arrange
        $token = $this->getAuthToken('reader@example.com', 'password3');
        $articleData = json_encode(['title' => 'Reader Article', 'content' => '...']);

        // Act + Assert for CREATE
        $this->client->request('POST', '/articles', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], (string) $articleData);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // Act + Assert for UPDATE
        $this->client->request('PUT', '/articles/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], (string) $articleData);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // Act + Assert for DELETE
        $this->client->request('DELETE', '/articles/1', [], [], ['HTTP_AUTHORIZATION' => 'Bearer '.$token]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test reader cannot manage users.
     */
    public function testReaderCannotManageUsers(): void
    {
        // Arrange
        $token = $this->getAuthToken('reader@example.com', 'password3');

        // Act
        $this->client->request('GET', '/users', [], [], ['HTTP_AUTHORIZATION' => 'Bearer '.$token]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test author can create articles.
     */
    public function testAuthorCanCreateArticle(): void
    {
        // Arrange
        $token = $this->getAuthToken('author@example.com', 'password2');
        $articleData = json_encode(['title' => 'Author Article', 'content' => '...']);

        // Act
        $this->client->request('POST', '/articles', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], (string) $articleData);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    /**
     * Test author can edit and delete their own articles.
     */
    public function testAuthorCanEditAndDeleteOwnArticle(): void
    {
        // Arrange
        $authorToken = $this->getAuthToken('author@example.com', 'password2');
        $articleId = $this->createArticleAsUser($authorToken, 'Author Own Article');

        // Act + Assert for UPDATE
        $this->client->request('PUT', "/articles/{$articleId}", [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$authorToken,
        ], (string) json_encode(['title' => 'Updated by Author']));
        $this->assertResponseIsSuccessful();

        // Act + Assert for DELETE
        $this->client->request('DELETE', "/articles/{$articleId}", [], [], ['HTTP_AUTHORIZATION' => 'Bearer '.$authorToken]);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    /**
     * Test author cannot edit or delete another user's articles.
     */
    public function testAuthorCannotEditOrDeleteAnotherUsersArticle(): void
    {
        // Arrange
        $adminToken = $this->getAuthToken('admin@example.com', 'password1');
        $articleId = $this->createArticleAsUser($adminToken, 'Admin Article');
        $authorToken = $this->getAuthToken('author@example.com', 'password2');

        // Act + Assert for UPDATE
        $this->client->request('PUT', "/articles/{$articleId}", [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$authorToken,
        ], (string) json_encode(['title' => 'Attempted Edit']));
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // Act + Assert for DELETE
        $this->client->request('DELETE', "/articles/{$articleId}", [], [], ['HTTP_AUTHORIZATION' => 'Bearer '.$authorToken]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test author cannot manage users.
     */
    public function testAuthorCannotManageUsers(): void
    {
        // Arrange
        $token = $this->getAuthToken('author@example.com', 'password2');

        // Act
        $this->client->request('GET', '/users', [], [], ['HTTP_AUTHORIZATION' => 'Bearer '.$token]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * Test admin can manage users.
     */
    public function testAdminCanManageUsers(): void
    {
        // Arrange
        $token = $this->getAuthToken('admin@example.com', 'password1');

        // Act
        $this->client->request('GET', '/users', [], [], ['HTTP_AUTHORIZATION' => 'Bearer '.$token]);

        // Assert
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test admin can create users.
     */
    public function testAdminCanCreateUser(): void
    {
        // Arrange
        $token = $this->getAuthToken('admin@example.com', 'password1');
        $userData = json_encode([
            'email' => 'newuserbyadmin@test.com',
            'password' => 'StrongPassword123!',
            'name' => 'New User by Admin',
            'role' => 'reader',
        ]);

        // Act
        $this->client->request('POST', '/users', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], (string) $userData);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    /**
     * Test admin can edit and delete any user's articles.
     */
    public function testAdminCanEditAndDeleteAnotherUsersArticle(): void
    {
        // Arrange
        $authorToken = $this->getAuthToken('author@example.com', 'password2');
        $articleId = $this->createArticleAsUser($authorToken, 'Article by Author');
        $adminToken = $this->getAuthToken('admin@example.com', 'password1');

        // Act
        $this->client->request('PUT', "/articles/{$articleId}", [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$adminToken,
        ], (string) json_encode(['title' => 'Updated by Admin']));
        $this->assertResponseIsSuccessful();

        // Act
        $this->client->request('DELETE', "/articles/{$articleId}", [], [], ['HTTP_AUTHORIZATION' => 'Bearer '.$adminToken]);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    /**
     * Helper method to get JWT token for user.
     */
    private function getAuthToken(string $username, string $password): string
    {
        $this->client->request('POST', '/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], (string) json_encode(['username' => $username, 'password' => $password]));

        $this->assertResponseIsSuccessful();
        $responseContent = (string) $this->client->getResponse()->getContent();
        /** @var array{token: string}|null $response */
        $response = json_decode($responseContent, true);

        $this->assertIsArray($response, 'Login response is not valid JSON.');
        $this->assertArrayHasKey('token', $response, 'Login response does not contain a token.');

        return $response['token'];
    }

    /**
     * Helper method to create article as user.
     */
    private function createArticleAsUser(string $token, string $title): int
    {
        $this->client->request('POST', '/articles', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], (string) json_encode(['title' => $title, 'content' => '...']));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        /** @var array{id: int} $response */
        $response = json_decode((string) $this->client->getResponse()->getContent(), true);

        return $response['id'];
    }
}
