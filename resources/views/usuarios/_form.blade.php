@php
    $ehEdicao = isset($usuario) && $usuario;
@endphp

<form method="POST"
  action="{{ $ehEdicao ? route('usuarios.update', $usuario) : route('usuarios.store') }}"
  class="needs-validation {{ $errors->any() ? 'was-validated' : '' }}" novalidate>
  @csrf
  @if ($ehEdicao)
    @method('PUT')
  @endif

  <div class="row g-3">
    <div class="col-12 col-xl-6">
      <section class="panel p-4 h-100">
        <h2 class="h5 section-title mb-4">
          <i class="bi bi-person" aria-hidden="true"></i> Dados pessoais
        </h2>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label" for="nome">Nome</label>
            <input class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" required
              maxlength="60" value="{{ old('nome', $usuario->name ?? '') }}" />
            <div class="invalid-feedback">@error('nome') {{ $message }} @else Informe o nome. @enderror</div>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="sobrenome">Sobrenome</label>
            <input class="form-control @error('sobrenome') is-invalid @enderror" id="sobrenome" name="sobrenome" required
              maxlength="80" value="{{ old('sobrenome', $usuario->sobrenome ?? '') }}" />
            <div class="invalid-feedback">@error('sobrenome') {{ $message }} @else Informe o sobrenome. @enderror</div>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="cpf">CPF</label>
            <input class="form-control @error('cpf') is-invalid @enderror" id="cpf" name="cpf" inputmode="numeric"
              maxlength="14" placeholder="000.000.000-00" value="{{ old('cpf', $usuario->cpf ?? '') }}" />
            @error('cpf')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label" for="nascimento">Data de nascimento</label>
            <input class="form-control @error('nascimento') is-invalid @enderror" id="nascimento" name="nascimento"
              type="date" required
              value="{{ old('nascimento', isset($usuario) && $usuario->nascimento ? $usuario->nascimento->format('Y-m-d') : '') }}" />
            <div class="invalid-feedback">@error('nascimento') {{ $message }} @else Informe a data de nascimento. @enderror</div>
          </div>
        </div>
      </section>
    </div>

    <div class="col-12 col-xl-6">
      <section class="panel p-4 h-100">
        <h2 class="h5 section-title mb-4">
          <i class="bi bi-envelope" aria-hidden="true"></i> Contato
        </h2>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label" for="email">E-mail</label>
            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" required
              maxlength="120" value="{{ old('email', $usuario->email ?? '') }}" />
            <div class="invalid-feedback">@error('email') {{ $message }} @else Informe um e-mail válido. @enderror</div>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="celular">Celular</label>
            <input class="form-control @error('celular') is-invalid @enderror" id="celular" name="celular" inputmode="tel"
              maxlength="15" required placeholder="(00) 00000-0000"
              value="{{ old('celular', $usuario->celular ?? '') }}" />
            <div class="invalid-feedback">@error('celular') {{ $message }} @else Informe o celular. @enderror</div>
          </div>
        </div>
      </section>
    </div>

    <div class="col-12">
      <section class="panel p-4">
        <h2 class="h5 section-title mb-4">
          <i class="bi bi-geo-alt" aria-hidden="true"></i> Endereço
        </h2>
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label" for="cep">CEP</label>
            <input class="form-control @error('cep') is-invalid @enderror" id="cep" name="cep" inputmode="numeric"
              maxlength="9" placeholder="00000-000" value="{{ old('cep', $usuario->cep ?? '') }}" />
            @error('cep')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-7">
            <label class="form-label" for="logradouro">Logradouro</label>
            <input class="form-control" id="logradouro" name="logradouro" maxlength="120"
              value="{{ old('logradouro', $usuario->logradouro ?? '') }}" />
          </div>
          <div class="col-md-2">
            <label class="form-label" for="numero">Número</label>
            <input class="form-control" id="numero" name="numero" maxlength="20"
              value="{{ old('numero', $usuario->numero ?? '') }}" />
          </div>
          <div class="col-md-4">
            <label class="form-label" for="complemento">Complemento</label>
            <input class="form-control" id="complemento" name="complemento" maxlength="80"
              value="{{ old('complemento', $usuario->complemento ?? '') }}" />
          </div>
          <div class="col-md-4">
            <label class="form-label" for="bairro">Bairro</label>
            <input class="form-control" id="bairro" name="bairro" maxlength="80"
              value="{{ old('bairro', $usuario->bairro ?? '') }}" />
          </div>
          <div class="col-md-3">
            <label class="form-label" for="cidade">Cidade</label>
            <input class="form-control" id="cidade" name="cidade" maxlength="80"
              value="{{ old('cidade', $usuario->cidade ?? '') }}" />
          </div>
          <div class="col-md-1">
            <label class="form-label" for="estado">UF</label>
            <input class="form-control" id="estado" name="estado" maxlength="2"
              value="{{ old('estado', $usuario->estado ?? '') }}" />
          </div>
        </div>
      </section>
    </div>

    <div class="col-12">
      <section class="panel p-4">
        <h2 class="h5 section-title mb-4">
          <i class="bi bi-shield-lock" aria-hidden="true"></i>
          Acesso e configurações
        </h2>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label" for="senha">Senha</label>
            <div class="input-group">
              <input class="form-control @error('senha') is-invalid @enderror" id="senha" name="senha" type="password"
                minlength="6" @if (!$ehEdicao) required @endif autocomplete="new-password" />
              <button class="btn btn-outline-secondary" type="button" data-toggle-password="senha"
                aria-label="Mostrar senha">
                <i class="bi bi-eye" aria-hidden="true"></i>
              </button>
              @error('senha')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            @if (!$ehEdicao)
              <div class="invalid-feedback">Informe uma senha com pelo menos 6 caracteres.</div>
            @endif
          </div>
          <div class="col-md-4">
            <label class="form-label" for="confirmacaoSenha">Confirmação de senha</label>
            <div class="input-group">
              <input class="form-control @error('senha_confirmation') is-invalid @enderror" id="confirmacaoSenha" name="senha_confirmation" type="password"
                @if (!$ehEdicao) required @endif autocomplete="new-password" />
              <button class="btn btn-outline-secondary" type="button" data-toggle-password="confirmacaoSenha"
                aria-label="Mostrar confirmação de senha">
                <i class="bi bi-eye" aria-hidden="true"></i>
              </button>
              @error('senha_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="invalid-feedback">As senhas devem ser iguais.</div>
          </div>
          <div class="col-md-2">
            <label class="form-label" for="perfil">Perfil</label>
            <select class="form-select" id="perfil" name="perfil">
              <option value="Administrador" @selected(old('perfil', $usuario->perfil ?? '') === 'Administrador')>Administrador</option>
              <option value="Operador" @selected(old('perfil', $usuario->perfil ?? 'Operador') === 'Operador')>Operador</option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-center">
            <div class="form-check form-switch mt-4">
              <input class="form-check-input" id="ativo" name="ativo" type="checkbox" value="1"
                @checked(old('ativo', $usuario->ativo ?? true)) />
              <label class="form-check-label" for="ativo">Cadastro ativo</label>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  <div class="d-flex flex-wrap gap-2 mt-3">
    <button class="btn btn-primary" type="submit">
      <i class="bi bi-check2" aria-hidden="true"></i>
      {{ $ehEdicao ? 'Atualizar usuário' : 'Salvar usuário' }}
    </button>
    <a class="btn btn-outline-secondary" href="{{ route('usuarios.index') }}">Voltar</a>
  </div>
</form>
