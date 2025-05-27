<?php

namespace Tests\Feature;

use App\Services\FileManipulatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileManipulatorServiceTest extends TestCase
{
    protected FileManipulatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FileManipulatorService();

        // テスト用のストレージディスクをクリア
        Storage::fake('public');
    }

    public function test_reverse_file_saves_to_correct_directory()
    {
        // テストファイルを作成
        $file = UploadedFile::fake()->createWithContent('test.txt', 'Hello World');

        // ファイル処理を実行
        $result = $this->service->processFile('reverse', $file, []);

        // 結果を検証
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('file-reverse/', $result['relative_path']);
        $this->assertStringContainsString('test.txt', $result['filename']);

        // ファイルが正しく保存されているか確認
        $this->assertTrue(Storage::disk('public')->exists($result['relative_path']));

        // ファイル内容が反転されているか確認
        $content = Storage::disk('public')->get($result['relative_path']);
        $this->assertEquals('dlroW olleH', $content);
    }

    public function test_copy_file_saves_to_correct_directory()
    {
        $file = UploadedFile::fake()->createWithContent('test.txt', 'Hello');

        $result = $this->service->processFile('copy', $file, []);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('file-copy/', $result['relative_path']);

        $this->assertTrue(Storage::disk('public')->exists($result['relative_path']));

        $content = Storage::disk('public')->get($result['relative_path']);
        $this->assertEquals('HelloHello', $content);
    }

    public function test_duplicate_file_saves_to_correct_directory()
    {
        $file = UploadedFile::fake()->createWithContent('test.txt', 'Test');

        $result = $this->service->processFile('duplicate-contents', $file, ['duplicate_count' => 3]);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('file-duplicate/', $result['relative_path']);

        $this->assertTrue(Storage::disk('public')->exists($result['relative_path']));

        $content = Storage::disk('public')->get($result['relative_path']);
        $this->assertEquals('TestTestTest', $content);
    }

    public function test_replace_string_saves_to_correct_directory()
    {
        $file = UploadedFile::fake()->createWithContent('test.txt', 'Hello World');

        $result = $this->service->processFile('replace-string', $file, [
            'search_string' => 'World',
            'replace_string' => 'PHP'
        ]);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('file-replace/', $result['relative_path']);

        $this->assertTrue(Storage::disk('public')->exists($result['relative_path']));

        $content = Storage::disk('public')->get($result['relative_path']);
        $this->assertEquals('Hello PHP', $content);
    }

    public function test_invalid_command_throws_exception()
    {
        $file = UploadedFile::fake()->createWithContent('test.txt', 'Hello');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('未対応のコマンドです。');

        $this->service->processFile('invalid-command', $file, []);
    }

    public function test_filename_includes_timestamp()
    {
        $file = UploadedFile::fake()->createWithContent('test.txt', 'Hello');

        $result = $this->service->processFile('reverse', $file, []);

        // ファイル名にタイムスタンプが含まれているか確認
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_test\.txt/', $result['filename']);
    }
}
