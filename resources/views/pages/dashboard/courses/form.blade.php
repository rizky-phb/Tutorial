@extends('pages.dashboard.layouts.main')
@section('head-script')
  <!-- Text Editor -->
  <script src="/js/tinymce_7.6.0/tinymce.min.js" referrerpolicy="origin"></script>
@endsection
@section('style')
  <style>
    .error-message {
      font-size: 14px;
    }

    #cover-preview,
    #cover-preview-update {
      max-width: 60%;
      width: 60%;
      height: auto;
      object-fit: cover;
      aspect-ratio: 16 / 9;
      cursor: pointer;
    }

    #cover-preview.dragging,
    #cover-preview-update.dragging {
      background-color: #0c9ce9;
    }

    @media (max-width: 767.98px) {

      #cover-preview,
      #cover-preview-update {
        max-width: 100%;
        width: 100%;
      }
    }
  </style>
@endsection
@section('main')
  <div class="d-flex justify-content-between flex-md-nowrap align-items-center border-bottom mb-3 flex-wrap pb-2 pt-3">
    <h3>{{ isset($course) ? 'Edit' : 'Add' }} Courses</h3>
    <div class="d-grid d-flex gap-2">
      <a class="btn btn-sm btn-warning" href="{{ route('courses.index') }}">
        <i class="ti ti-arrow-back-up"></i> Back
      </a>
      @if (isset($course))
        <a class="btn btn-sm btn-primary" href="{{ route('courses.show', $course->slug) }}">
          <i class="ti ti-eye"></i> Preview
        </a>
        <form action="{{ route('courses.destroy', $course->slug) }}" method="POST">
          @csrf
          @method('delete')
          <button class="btn btn-sm btn-danger delete-course-btn" id="delete">
            <i class="ti ti-trash"></i> Delete
          </button>
        </form>
      @endif
    </div>
  </div>
  <form action="{{ route(isset($course) ? 'courses.update' : 'courses.store', isset($course) ? $course->slug : '') }}" method="POST" enctype="multipart/form-data">
    @if (isset($course))
      @method('PATCH')
    @endif
    @csrf
    <div class="mt-2">
      <label class="form-label" for="title">Title</label>
      <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" type="text" value="{{ old('title', isset($course) ? $course->title : '') }}" placeholder="Course apa yang ingin kamu tambahkan?" required autofocus>
    </div>
    @error('title')
      <div class="text-danger error-message text-start">
        {{ $message }}
      </div>
    @enderror
    <div class="mt-4">
      <label class="form-label">Slug</label>
      <input class="form-control @error('slug') is-invalid @enderror" id="slug" type="text" value="{{ Illuminate\Support\Str::slug(old('title', isset($course) ? $course->title : '')) }}" placeholder="Slug akan terisi otomatis sesuai judul course yang kamu masukan." disabled>
    </div>
    @error('slug')
      <div class="text-danger error-message text-start">
        {{ $message }}
      </div>
    @enderror
    <div class="mt-4">
      <label class="form-label">Mata Pelajaran</label>
      <select class="form-select @error('category_id') is-invalid @enderror" id="category" name="category_id" required>
        <option selected>Pilih mata pelajaran...</option>
        @forelse ($categories as $category)
          <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : (isset($course) && $category->id == $course->category->id ? 'selected' : '') }}>{{ $category->name }}</option>
        @empty
          <option>Mata pelajaran tidak ditemukan, silahkan tambahkan terlebih dahulu</option>
        @endforelse
      </select>
    </div>
    @error('category_id')
      <div class="text-danger error-message text-start">
        {{ $message }}
      </div>
    @enderror
    <div class="mt-4">
      <label class="form-label" for="desc">Description</label>
      <textarea class="form-control @error('desc') is-invalid @enderror" id="desc" name="desc" rows="8" placeholder="Apa yang akan dipelajari di course ini?" required>{{ old('desc', isset($course) ? $course->desc : '') }}</textarea>
    </div>
    @error('desc')
      <div class="text-danger error-message text-start">
        {{ $message }}
      </div>
    @enderror
    <div class="mt-4">
      <label class="form-label d-block" for="cover">Cover</label>
      <img class="img-thumbnail img-fluid mb-2" id="cover-preview{{ isset($course) ? '-update' : '' }}" src="{{ isset($course) ? $course->cover : '/img/assets/drag-drop-upload.png' }}" alt="cover {{ isset($course) ? 'preview' : '' }}" old-src="{{ isset($course) ? $course->cover : '' }}">
      <br>
      <span> Ukuran file maksimal <span class="badge text-bg-dark">5MB</span>
        dan format gambar yang didukung:
        <span class="badge text-bg-primary">PNG</span>
        <span class="badge text-bg-secondary">JPG</span>
        <span class="badge text-bg-success">JPEG</span>
        <span class="badge text-bg-danger">GIF</span>
        <span class="badge text-bg-warning">JFIF</span>
        <span class="badge text-bg-info">WEBP</span>
      </span>
      @error('cover')
        <div class="text-danger error-message text-start">
          {{ $message }}
        </div>
      @enderror
      <input class="d-none form-control" id="cover-input" id="cover" name="cover" type="file" accept="image/*">
    </div>
    <div class="mt-4">
      <label class="form-label" for="body">Body</label>
      <textarea id="tinymce" name="body">{{ old('body', isset($course) ? $course->body : '') }}</textarea>
    </div>
    @error('body')
      <div class="text-danger error-message text-start">
        {{ $message }}
      </div>
    @enderror
    <div class="d-grid d-flex justify-content-end mt-3 gap-2">
      <button class="btn btn-primary" id="update-btn" name="submit" type="submit" value="done">{{ isset($course) ? 'Update' : 'Tambah' }}
        Course</button>
      <button class="btn btn-warning" id="draft-btn" name="submit" type="submit" value="draft">Simpan
        Draft</button>
      <a class="btn btn-danger" href="{{ route('courses.index') }}">Cancel</a>
    </div>
  </form>
@endsection
@section('script')
  <script>
    $(document).ready(function() {
      tinymce.init({
        selector: '#tinymce',
        height: 700,
        license_key: 'gpl',
        plugins: 'fullscreen preview anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'fullscreen preview undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        file_picker_types: 'image media',
        image_caption: true,
        file_picker_callback: function(cb, value, meta) {
          const input = $('<input/>')
            .attr('type', 'file')
            .attr('accept', 'image/*')
            .on('change', function(e) {
              const file = e.target.files[0];
              const reader = new FileReader();
              reader.onload = function() {
                const id = 'blobid' + (new Date()).getTime();
                const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                const base64 = reader.result.split(',')[1];
                const blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);
                cb(blobInfo.blobUri(), {
                  title: file.name
                });
              };
              reader.readAsDataURL(file);
            });
          input.trigger('click');
        },
      });

      let delayTimer;
      $('#title').on('keyup', function() {
        clearTimeout(delayTimer);
        delayTimer = setTimeout(() => {
          $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            data: {
              string: $(this).val()
            },
            url: `{{ route('getSlug', [], false) }}`,
            success: function(response) {
              $('#slug').val(response);
            }
          });
        }, 500);
      });

      // Image preview and drag n' drop
      const coverInput = $('#cover-input');
      const coverPreview = $('#cover-preview');
      const coverPreviewUpdate = $('#cover-preview-update');
      const coverPreviewAndUpdate = $('#cover-preview, #cover-preview-update');

      function imagePreview(files) {
        const file = files;
        const fileType = file["type"];
        const validImageTypes = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/jfif",
          "image/webp"
        ];
        const placeholder_src = '/img/assets/drag-drop-upload.png';
        const old_src = coverPreviewUpdate.attr('old-src');
        const invalidTypeText =
          `<div class="d-flex d-grid gap-2 mt-2 justify-content-center">
                        <span class="badge text-bg-primary">PNG</span>
                        <span class="badge text-bg-secondary">JPG</span>
                        <span class="badge text-bg-success">JPEG</span>
                        <span class="badge text-bg-danger">GIF</span>
                        <span class="badge text-bg-warning">JFIF</span>
                        <span class="badge text-bg-info">WEBP</span>
                    </div>`
        const invalidSizeText = '<span class="badge text-bg-dark">5MB</span>'
        if ($.inArray(fileType, validImageTypes) < 0) {
          coverPreview.attr('src', placeholder_src);
          coverPreviewUpdate.attr('src', old_src);
          coverInput.val('')
          swalCustom.fire({
            icon: 'warning',
            html: 'Ekstensi file yang didukung: <br>' + invalidTypeText,
          })
        } else if (file.size > 5242880) {
          coverPreview.attr('src', placeholder_src);
          coverPreviewUpdate.attr('src', old_src);
          coverInput.val('')
          swalCustom.fire({
            icon: 'warning',
            html: 'Ukuran file maksimal ' + invalidSizeText,
          })
        } else {
          let reader = new FileReader();
          reader.onload = function(e) {
            coverPreview.attr('src', e.target.result);
            coverPreviewUpdate.attr('src', e.target.result);
          }
          reader.readAsDataURL(file);
        }
      };
      coverPreviewAndUpdate.on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        coverPreviewAndUpdate.addClass('dragging');
      });
      coverPreviewAndUpdate.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        coverPreviewAndUpdate.removeClass('dragging');
      });
      coverPreviewAndUpdate.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
          coverInput.prop('files', files);
          imagePreview(files[0]);
        }
        coverPreviewAndUpdate.removeClass('dragging');
      });
      coverPreviewAndUpdate.click(function(e) {
        e.preventDefault();
        coverInput.click();
      });
      coverInput.change(function(e) {
        e.preventDefault();
        imagePreview(this.files[0]);
      });

      // TinyMCE watermark remover
      waitForElm('.tox-statusbar__branding').then((elm) => {
        $('.tox-statusbar__branding').remove();
      });
    });
  </script>
@endsection
