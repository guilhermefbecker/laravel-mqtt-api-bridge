<form method="POST"
  action="{{ $semaforo ? route('semaforos.update', $semaforo) : route('semaforos.store') }}"
  class="needs-validation {{ $errors->any() ? 'was-validated' : '' }}" novalidate>
  @csrf
  @if ($semaforo)
    @method('PUT')
  @endif

  <div class="mb-3">
    <label for="nome" class="form-label">Nome do semáforo</label>
    <input class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" required maxlength="60"
      value="{{ old('nome', $semaforo->nome ?? '') }}" placeholder="Ex.: Avenida Central" />
    <div class="invalid-feedback">@error('nome') {{ $message }} @else Informe um nome. @enderror</div></div>
  </div>

  <div class="mb-3">
    <label for="tempo_vermelho" class="form-label">Vermelho (ms)</label>
    <input class="form-control @error('tempo_vermelho') is-invalid @enderror" id="tempo_vermelho" name="tempo_vermelho"
      type="number" min="100" required value="{{ old('tempo_vermelho', $semaforo->tempo_vermelho ?? 5000) }}" />
    <div class="invalid-feedback">@error('tempo_vermelho') {{ $message }} @else Use um valor a partir de 100 ms. @enderror</div>
  </div>

  <div class="mb-3">
    <label for="tempo_amarelo" class="form-label">Amarelo (ms)</label>
    <input class="form-control @error('tempo_amarelo') is-invalid @enderror" id="tempo_amarelo" name="tempo_amarelo"
      type="number" min="100" required value="{{ old('tempo_amarelo', $semaforo->tempo_amarelo ?? 2000) }}" />
    <div class="invalid-feedback">@error('tempo_amarelo') {{ $message }} @else Use um valor a partir de 100 ms. @enderror</div>
  </div>

  <div class="mb-3">
    <label for="tempo_verde" class="form-label">Verde (ms)</label>
    <input class="form-control @error('tempo_verde') is-invalid @enderror" id="tempo_verde" name="tempo_verde"
      type="number" min="100" required value="{{ old('tempo_verde', $semaforo->tempo_verde ?? 6000) }}" />
    <div class="invalid-feedback">@error('tempo_verde') {{ $message }} @else Use um valor a partir de 100 ms. @enderror</div>
  </div>

  <div class="mb-3">
    <label for="latitude_longitude" class="form-label">Latitude e Longitude do Semáforo</label>
    <input class="form-control @error('latitude_longitude') is-invalid @enderror" id="latitude_longitude"
      name="latitude_longitude" type="text" value="{{ old('latitude_longitude', $semaforo->latitude_longitude ?? '') }}" />
    @error('latitude_longitude')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="form-check form-switch mb-4">
    <input class="form-check-input" id="ativo" name="ativo" type="checkbox" value="1"
      @checked(old('ativo', $semaforo ? $semaforo->ativo : true)) />
    <label class="form-check-label" for="ativo">Cadastro ativo</label>
  </div>

  <div class="d-flex flex-wrap gap-2">
    <button class="btn btn-primary" type="submit">
      <i class="bi bi-check2" aria-hidden="true"></i>
      {{ $semaforo ? 'Atualizar cadastro' : 'Salvar cadastro' }}
    </button>
    @if ($semaforo)
      <a class="btn btn-outline-secondary" href="{{ route('semaforos.index') }}">Cancelar</a>
    @else
      <a class="btn btn-outline-secondary" href="{{ route('semaforos.create') }}">Limpar</a>
    @endif
    <a class="btn btn-outline-secondary" href="{{ route('semaforos.index') }}"><i class="bi bi-arrow-left"
        aria-hidden="true"></i> Voltar</a>
  </div>
</form>
